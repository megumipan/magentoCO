<?php

class Rack_Point_Model_Source_Active
{
    const ORDER      = 1;
    const INVOICE    = 2;
    
    public function toOptionArray()
    {
        return array(
           self::ORDER => Mage::helper('rackpoint')->__('After placing order'),
           self::INVOICE   => Mage::helper('rackpoint')->__('After creating invoice'),
        );
    }
}