<?php

class Rack_Point_Model_Mysql4_Rule_Product extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init('rackpoint/rule_product', 'rule_product_id');
    }
}