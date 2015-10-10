<?php

class Rack_Point_Model_Mysql4_Point_History extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct() 
    {
        $this->_init('rackpoint/point_history', 'id');
    }
}