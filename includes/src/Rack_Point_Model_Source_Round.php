<?php

class Rack_Point_Model_Source_Round
{
    const ROUND_MODE_ROUND  = 1;
    const ROUND_MODE_CEIL   = 2;
    const ROUND_MODE_FLOOR  = 3;
    
    public function toOptionArray()
    {
        return array(
           self::ROUND_MODE_ROUND  => Mage::helper('rackpoint')->__('Round'),
           self::ROUND_MODE_CEIL   => Mage::helper('rackpoint')->__('Ceil'),
           self::ROUND_MODE_FLOOR  => Mage::helper('rackpoint')->__('Floor')
        );
    }
}