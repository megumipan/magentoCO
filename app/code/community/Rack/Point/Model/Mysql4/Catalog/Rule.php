<?php

class Rack_Point_Model_Mysql4_Catalog_Rule extends Mage_Core_Model_Mysql4_Abstract
{
    const SECONDS_IN_DAY = 86400;
    
    protected function _construct()
    {
        $this->_init('rackpoint/catalog_rule', 'rule_id');
    }
    
    /**
     * Prepare object data for saving
     *
     * @param Mage_Core_Model_Abstract $object
     */
    public function _beforeSave(Mage_Core_Model_Abstract $object)
    {
        if (!$object->getFromDate()) {
            $date = Mage::app()->getLocale()->date();
            $date->setHour(0)
                ->setMinute(0)
                ->setSecond(0);
            $object->setFromDate($date);
        }
        if ($object->getFromDate() instanceof Zend_Date) {
            $object->setFromDate($object->getFromDate()->toString(Varien_Date::DATETIME_INTERNAL_FORMAT));
        }

        if (!$object->getToDate()) {
            $object->setToDate(new Zend_Db_Expr('NULL'));
        }
        else {
            if ($object->getToDate() instanceof Zend_Date) {
                $object->setToDate($object->getToDate()->toString(Varien_Date::DATETIME_INTERNAL_FORMAT));
            }
        }
        if (is_array($object->getWebsiteIds())) {
            $object->setWebsiteIds(implode(',', $object->getWebsiteIds()));
        }
        if (is_array($object->getCustomerGroupIds())) {
            $object->setCustomerGroupIds(implode(',', $object->getCustomerGroupIds()));
        }

        parent::_beforeSave($object);
    }
    
    /**
     * Update products which are matched for rule
     *
     * @param   Rack_Point_Model_Catalog_Rule $rule
     * @return  Rack_Point_Model_Mysql4_Catalog_Rule
     */
    public function updateRuleProductData(Rack_Point_Model_Catalog_Rule $rule)
    {
        $ruleId = $rule->getId();
        $write = $this->_getWriteAdapter();
        $write->beginTransaction();

        $write->delete($this->getTable('rackpoint/rule_product'), $write->quoteInto('rule_id=?', $ruleId));

        if (!$rule->getIsActive()) {
            $write->commit();
            return $this;
        }
        
        $websiteIds = $rule->getWebsiteIds();
        if (!is_array($websiteIds)) {
            $websiteIds = explode(',', $websiteIds);
        }
        if (empty($websiteIds)) {
            return $this;
        }
        $productIds = $rule->getMatchingProductIds();
        $customerGroupIds = $rule->getCustomerGroupIds();

        $fromTime = strtotime($rule->getFromDate());
        $fromTime = $fromTime ? ($fromTime - self::SECONDS_IN_DAY + 1) : 0; //川井さんの追加
        $toTime = strtotime($rule->getToDate());
        $toTime = $toTime ? ($toTime + self::SECONDS_IN_DAY - 1) : 0;

        $sortOrder = (int)$rule->getSortOrder();
        $actionOperator = $rule->getSimpleAction();
        $actionAmount = $rule->getPointAmount();
        $actionStop = $rule->getStopRulesProcessing();

        $rows = array();
        $queryStart = 'INSERT INTO '.$this->getTable('rackpoint/rule_product').' (
                rule_id, from_time, to_time, website_id, customer_group_id, product_id, action_operator,
                action_amount, action_stop, sort_order ) values ';
        $queryEnd = ' ON DUPLICATE KEY UPDATE action_operator=VALUES(action_operator),
            action_amount=VALUES(action_amount), action_stop=VALUES(action_stop)';
        try {
            foreach ($productIds as $productId) {
                foreach ($websiteIds as $websiteId) {
                    foreach ($customerGroupIds as $customerGroupId) {
                        $rows[] = "('" . implode("','", array(
                            $ruleId,
                            $fromTime,
                            $toTime,
                            $websiteId,
                            $customerGroupId,
                            $productId,
                            $actionOperator,
                            $actionAmount,
                            $actionStop,
                            $sortOrder))."')";
                        /**
                         * Array with 1000 rows contain about 2M data
                         */
                        if (sizeof($rows)==1000) {
                            $sql = $queryStart.join(',', $rows).$queryEnd;
                            $write->query($sql);
                            $rows = array();
                        }
                    }
                }
            }
            if (!empty($rows)) {
                $sql = $queryStart.join(',', $rows).$queryEnd;
                $write->query($sql);
            }

            $write->commit();
        } catch (Exception $e) {
            $write->rollback();
            throw $e;
        }

        return $this;
    }
    
    public function updateRuleForProduct($rule, $product)
    {
        $ruleId = $rule->getId();
        $write = $this->_getWriteAdapter();

        //comment out because of nested transaction???
        //$write->beginTransaction();

        $write->delete($this->getTable('rackpoint/rule_product'), $write->quoteInto('rule_id=? AND product_id=?', $ruleId, $product->getId()));

        if (!$rule->getIsActive()) {
            //$write->commit();
            return $this;
        }

        $websiteIds = $rule->getWebsiteIds();
        if (!is_array($websiteIds)) {
            $websiteIds = explode(',', $websiteIds);
        }

        $productWebSites = $product->getWebsiteIds();
        $websiteIds = array_intersect($productWebSites, $websiteIds);
        if (empty($websiteIds)) {
            return $this;
        }
        $productIds = array($product->getId());
        $customerGroupIds = $rule->getCustomerGroupIds();

        $fromTime = strtotime($rule->getFromDate());
        $fromTime = $fromTime ? ($fromTime - self::SECONDS_IN_DAY + 1) : 0;

        $toTime = strtotime($rule->getToDate());
        $toTime = $toTime ? ($toTime + self::SECONDS_IN_DAY - 1) : 0;

        $sortOrder = (int)$rule->getSortOrder();
        $actionOperator = $rule->getSimpleAction();
        $actionAmount = $rule->getPointAmount();
        $actionStop = $rule->getStopRulesProcessing();

        $rows = array();
        $queryStart = 'INSERT INTO '.$this->getTable('rackpoint/rule_product').' (
                rule_id, from_time, to_time, website_id, customer_group_id, product_id, action_operator,
                action_amount, action_stop, sort_order ) values ';
        $queryEnd = ' ON DUPLICATE KEY UPDATE action_operator=VALUES(action_operator),
            action_amount=VALUES(action_amount), action_stop=VALUES(action_stop)';
        try {
            foreach ($productIds as $productId) {
                foreach ($websiteIds as $websiteId) {
                    foreach ($customerGroupIds as $customerGroupId) {
                        $rows[] = "('" . implode("','", array(
                            $ruleId,
                            $fromTime,
                            $toTime,
                            $websiteId,
                            $customerGroupId,
                            $productId,
                            $actionOperator,
                            $actionAmount,
                            $actionStop,
                            $sortOrder))."')";
                        /**
                         * Array with 1000 rows contain about 2M data
                         */
                        if (sizeof($rows)==1000) {
                            $sql = $queryStart.join(',', $rows).$queryEnd;
                            $write->query($sql);
                            $rows = array();
                        }
                    }
                }
            }
            if (!empty($rows)) {
                $sql = $queryStart.join(',', $rows).$queryEnd;
                $write->query($sql);
            }

            //$write->commit();
        } catch (Exception $e) {
            //$write->rollback();
            throw $e;
        }

        return $this;
    }

    public function deleteRuleForProduct($product)
    {
        $write = $this->_getWriteAdapter();
        $write->delete($this->getTable('rackpoint/rule_product'), $write->quoteInto('product_id=?', $product->getId()));
        return $this;
    }

    public function isApplyAll()
    {
        $select = 'SELECT rule_id FROM ' . $this->getTable('rackpoint/catalog_rule')
                . ' WHERE is_active = 1 AND rule_id NOT IN (SELECT rule_id FROM ' . $this->getTable('rackpoint/rule_product') . ')';
        $result = $this->_getReadAdapter()->fetchOne($select);
        if ($result) {
            return false;
        }

        return true;
    }
}
