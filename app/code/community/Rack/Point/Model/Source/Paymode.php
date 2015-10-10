<?php

class Rack_Point_Model_Source_Paymode
{
    const PAYMODE_PRODUCT_FIRST  = 1;
    const PAYMODE_EXTFEE_FIRST   = 2;
    
    public function toOptionArray()
    {
        return array(
           self::PAYMODE_PRODUCT_FIRST => Mage::helper('rackpoint')->__('Pay for product first then extra fees'),
           self::PAYMODE_EXTFEE_FIRST   => Mage::helper('rackpoint')->__('Pay for extra fees first then product'),
        );
    }
}