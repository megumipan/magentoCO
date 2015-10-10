<?php

class Rack_Point_Model_Mysql4_Point_Balance extends Mage_Core_Model_Mysql4_Abstract
{
    const BULK_INSERT_ROWS = 100;
    
    protected function _construct() 
    {
        $this->_init('rackpoint/point_balance', 'id');
    }
    
    public function addPoint($customerId, $point, $websiteId, $updateExpired = true)
    {
        $current = new Zend_Date();

        
        $query = 'UPDATE `' . $this->getMainTable() .'`'
               . ' SET balance = balance + ' . $point
               . ', updated_at = "' . $current->toString(Varien_Date::DATETIME_INTERNAL_FORMAT) . '"';
        if ($updateExpired == true) {
            $query .= ', expired_at = "' . Mage::helper('rackpoint')->getPointExpireDate($current) . '"';
        }

        $query .= ', notify_times = 0'
               . ' WHERE customer_id = "' . intval($customerId) . '" AND website_id="' . intval($websiteId) . '"';

        $this->_getWriteAdapter()->query($query);
    }
    
    public function subPoint($customerId, $point, $websiteId, $updateExpired = true)
    {
        $current = new Zend_Date();
        
        $query = 'UPDATE `' . $this->getMainTable() .'`'
               . ' SET balance = IF(balance > ' . $point . ', balance - ' . $point . ', 0)'
               . ', updated_at = "' . $current->toString(Varien_Date::DATETIME_INTERNAL_FORMAT) . '"';
        if ($updateExpired == true) {
            $query .= ', expired_at = "' . Mage::helper('rackpoint')->getPointExpireDate($current) . '"';
        }

        $query .= ', notify_times = 0'
               . ' WHERE customer_id = "' . intval($customerId) . '" AND website_id="' . intval($websiteId) . '"';
        
        
        $this->_getWriteAdapter()->query($query);
    }
    
    public function loadByCustomerId($object, $customerId, $websiteId = null)
    {
        if ($websiteId == null) {
            $websiteId = Mage::app()->getStore()->getWebsiteId();
        }
        
        $read = $this->_getReadAdapter();
        if ($read) {
            $select = 'SELECT * FROM `' . $this->getMainTable() . '` WHERE customer_id = "' . $customerId . '" AND website_id = "' . $websiteId . '"';
            $data = $read->fetchRow($select);
            
            if ($data) {
                $object->setData($data);
            }    
        }
        
        return $this;
    }
    
    public function renew($customerId, $websiteId, $month = 1)
    {
        $now = date('Y-m-d H:i:s');
        $sql = 'UPDATE `' . $this->getMainTable() 
             . "` SET expired_at = DATE_ADD(expired_at, INTERVAL $month MONTH), notify_times=0, lastest_notify=NULL, updated_at='{$now}' "
             . "WHERE customer_id={intval($customerId)} AND website_id={intval($websiteId)}";
        
        $this->_getWriteAdapter()->exec($sql);
        
        return $this;
    }
    
    public function import($filename, $website = null) 
    {
        if (!file_exists($filename)) {
            return Mage::helper('rackpoint')->__('File not found!');
        }
        $encoding = Mage::getStoreConfig('rackpoint/import/encoding');
        $csvReader = new Rack_Point_Model_MbCsvReader($filename, ',', $encoding);
        $current = new Zend_Date();
        $createdAt = $current->toString(Varien_Date::DATETIME_INTERNAL_FORMAT);
        $expired = Mage::helper('rackpoint')->getPointExpirePeriod();
        $expiredAt = $current->add($expired, Zend_Date::MONTH)->toString(Varien_Date::DATETIME_INTERNAL_FORMAT);
        
//        if ($website == null) {
//            $website = Mage::app('default')->getDefaultStoreView()->getWebsiteId();
//        }
        $storeTimeZone = new DateTimeZone(Mage::getStoreConfig(Mage_Core_Model_Locale::XML_PATH_DEFAULT_TIMEZONE));
        $utcTimeZone   = new DateTimeZone('UTC');
        $adapter = $this->_getWriteAdapter();

        /* @var $_helper Rack_Point_Helper_Data */
        $_helper = Mage::helper('rackpoint');
        try {
            $adapter->beginTransaction();
            $csvReader->pass();
            $rowNum = 1;
            while ($csvReader->next()) {
                $row = $csvReader->current();

                if(count($row) == 0) {
                    continue;
                }
                $customerId = $this->_getCustomerIdByEmail($adapter, $row['email']);
                if (!$customerId) {
                    Mage::log($this->__('Customer with email %s not found', $row['email']), Zend_Log::CRIT, 'import_point.log', true);
                    continue;
                }
                if (isset($row['website_id']) && $website == null) {
                    $row[':website_id'] = $row['website_id'];
                } else {
                    $row[':website_id'] = $website;
                }

                $row[':created_at'] = $createdAt;
                if (!isset($row['expired_at']) || trim($row['expired_at']) == '') {
                    $row[':expired_at'] = $expiredAt;
                } else {
                    $row[':expired_at'] = $this->parseImportDate($row['expired_at'], $storeTimeZone, $utcTimeZone);
                }
                if (!isset($row['updated_at']) || trim($row['updated_at']) == '') {
                    // Data does not have updated_at value. So using current datetime.
                    // The value of expired_at will be ignore in this case.
                    // expired_at will be calculate by using current point settings.
                    $row[':updated_at'] = $createdAt;
                    $row[':expired_at'] = $expiredAt;
                } else {
                    $row[':updated_at'] = $this->parseImportDate($row['updated_at'], $storeTimeZone, $utcTimeZone);
                    if (!isset($row['expired_at']) || trim($row['expired_at']) == '') {
                        $dateObj = DateTime::createFromFormat('Y-m-d H:i:s', $row[':updated_at']);
                        $dateObj->add(new DateInterval("P{$expired}M"));
                        $row[':expired_at'] = $dateObj->format('Y-m-d H:i:s');
                    }
                }
                $row[':customer_id'] = $customerId;
                $row[':point'] = $row['point'];

                //get comment.
                $comment = isset($row['comment']) ? $row['comment'] : '';
                unset($row['comment']);

                //import points.
                $query = $this->_createInsertQuery($row);
                $adapter->query($query, $row);

                //write log
                $row['rate'] = $_helper->getPointRate();
                $this->_insertLog($adapter, $row, $comment);
                $rowNum++;
            }

            $adapter->commit();
        } catch (Exception $e) {
            $adapter->rollback();
            //Log data of error row if any.
            if (isset($row) && is_array($row)) {
                Mage::log(print_r($row, true), Zend_Log::CRIT, 'import_point.log', true);
            }
            $message = $_helper->__('Error at row %s with message: %s', $rowNum, $e->getMessage());
            Mage::throwException($message);
        }
        
        return true;
    }

    protected function _createInsertQuery(&$row)
    {
        unset($row['email']);
        unset($row['point']);
        unset($row['updated_at']);
        unset($row['expired_at']);
        unset($row['website_id']);

        $query = 'INSERT INTO ' . $this->getTable('rackpoint/point_balance') . ' (customer_id, website_id, balance, created_at, updated_at, expired_at) '
               . 'VALUES (:customer_id, :website_id, :point, :created_at, :updated_at, :expired_at) ';
        if (strpos($row[':point'], '+') === 0) {
            $query .= ' ON DUPLICATE KEY UPDATE balance = balance + VALUES(balance), updated_at=VALUES(updated_at), expired_at=GREATEST(expired_at, VALUES(expired_at)) ';
        } else if (strpos($row[':point'], '-') === 0) {
            $query .= ' ON DUPLICATE KEY UPDATE balance = GREATEST(0, balance - ABS(VALUES(balance))), updated_at=VALUES(updated_at), expired_at=GREATEST(expired_at, VALUES(expired_at)) ';
        } else {
            $query .= ' ON DUPLICATE KEY UPDATE balance = VALUES(balance), updated_at=VALUES(updated_at), expired_at=GREATEST(expired_at, VALUES(expired_at)) ';
        }

        return $query;
    }

    protected function _insertLog($adapter, $row, $comment)
    {
        if (empty($comment)) {
            $comment = Mage::getStoreConfig('rackpoint/import/comment', $row[':website_id']);
        }
        $params = array(
            'customer_id'   => $row[':customer_id'],
            'website_id'    => $row[':website_id'],
            'action'        => Rack_Point_Model_Point_History::ACTION_IMPORT,
            'point'         => $row[':point'],
            'comment'       => $comment,
            'created_at'    => $row[':updated_at'],
            'reg_id'        => 'none',
            'rate'          => $row['rate']
        );

        $query = 'INSERT INTO ' . $this->getTable('rackpoint/point_history')
               . '(customer_id, website_id, action, point, comment, created_at, ref_id, rate) VALUES ('
               . ':customer_id, :website_id, :action, :point, :comment, :created_at, :reg_id, :rate)';

        $adapter->query($query, $params);
    }

    /**
     * Parse date string to GMT date to save to DB
     *
     * @param $date
     * @param $timeZone
     * @param $utcTimeZone
     *
     * @return string
     */
    protected function parseImportDate($date, $timeZone, $utcTimeZone)
    {
        $date = DateTime::createFromFormat('Y-m-d H:i:s', $date, $timeZone);
        if ($date == null) {
            throw new Exception(Mage::helper('rackpoint')->__('Invalid date format. Only YYYY-MM-DD HH:mm:ss is accepted.'));
        }
        $date->setTimezone($utcTimeZone);

        return $date->format('Y-m-d H:i:s');
    }
    
    protected function _getCustomerIdByEmail($reader, $email)
    {
        $sql = 'SELECT entity_id FROM ' . $this->getTable('customer/entity') . ' WHERE email=:email';
        
        $customerId = $reader->fetchOne($sql, array(':email' => $email));
        if (!$customerId) {
            Mage::throwException('Customer email not found: ' . $email);
        }
        
        return $customerId;
    }
    
    protected function _insertRows($writer, $rows)
    {
        $sql = 'INSERT INTO ' . $this->getTable('rackpoint/point_balance') 
             . '(customer_id, website_id, balance, created_at, updated_at, expired_at)'
             . ' VALUES (' . implode('),(', $rows) . ')'
             . ' ON DUPLICATE KEY UPDATE balance=VALUES(balance), updated_at=VALUES(updated_at)';
        
        $writer->query($sql);
    }

    public function resetExpiredPoint()
    {
        /* @var $helper Rack_Point_Helper_Data */
        $helper = Mage::helper('rackpoint');

        $connection = $this->_getWriteAdapter();

        $action  = $connection->quote($helper->__('Expired'));
        $comment = $connection->quote($helper->__('Reset expired point.'));
        $rate = $helper->getPointRate();
        $now = date('Y-m-d H:i:s'); //UTC current datetime;
        $expiredMonths = $helper->getPointExpirePeriod();
        $connection->beginTransaction();
        try {
            $historySql = ' INSERT INTO ' . $this->getTable('rackpoint/point_history')
                        . ' (customer_id, website_id, action, point, comment, created_at, ref_id, rate) '
                        . " (SELECT customer_id, website_id, {$action}, -balance, $comment, '{$now}', 'none', {$rate} "
                        . ' FROM ' . $this->getTable('rackpoint/point_balance')
                        . " WHERE balance > 0 AND expired_at <= '{$now}' "
                        . '    AND customer_id IN (SELECT entity_id FROM ' . $this->getTable('customer/entity') . '))';

            $resetSql = " UPDATE " . $this->getTable('rackpoint/point_balance')
                      . " SET balance = 0, notify_times = 0, expired_at = DATE_ADD(expired_at, INTERVAL $expiredMonths MONTH)"
                      . " WHERE balance > 0 AND expired_at <= '{$now}' "
                      . '    AND customer_id IN (SELECT entity_id FROM ' . $this->getTable('customer/entity') . ')';

            //Insert history data
            $connection->query($historySql);
            //Reset point
            $connection->query($resetSql);
            $connection->commit();

        } catch (Exception $e) {
            Mage::logException($e);
            $connection->rollBack();
        }

    }
}