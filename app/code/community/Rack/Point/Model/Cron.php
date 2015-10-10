<?php

class Rack_Point_Model_Cron
{
    public function expireNotify()
    {
        $balance = Mage::getModel('rackpoint/point_balance');
        
        $collection = $balance->getNotifyCollection();
        if ($collection == false) {
            return;
        }
        
        $collection->setPageSize(20);
        foreach ($collection->getItems() as $balance) {
            $balance->notify();
        }
    }

    /**
     * Clean expired point (reset balance to zero)
     */
    public function cleanExpiredPoint() {
        /* @var $balance Rack_Point_Model_Mysql4_Point_Balance */
        $balance = Mage::getResourceModel('rackpoint/point_balance');
        $balance->resetExpiredPoint();
    }

    public function import()
    {
        $enabled = Mage::getStoreConfig('rackpoint/import/enable');
        if ($enabled != 1) {
            return;
        }

        $filePath = Mage::getStoreConfig('rackpoint/import/path');
        if ($filePath == '') {
            $filePath = Mage::getBaseDir('var') . DS . 'import' . DS . 'points';
        }

        if (!file_exists($filePath)) {
            return;
        }
        $filePath = $filePath . DS . '*.csv';
        $files = glob($filePath);
        if (is_array($files)) {
            /* @var $balance Rack_Point_Model_Mysql4_Point_Balance */
            $balance = Mage::getResourceModel('rackpoint/point_balance');
            foreach ($files as $f) {
                try {
                    $result = $balance->import($f);
                    if ($result === true) {
                        unlink($f);
                    } else {
                        Mage::log($result, Zend_Log::CRIT, 'import_point.log', true);
                        rename($f, $f . '.error');
                    }
                } catch (Exception $e) {
                    rename($f, $f . '.error');
                    Mage::log($e->getMessage(), Zend_Log::CRIT, 'import_point.log', true);
                }

            }
        }
    }
}