<?php

class Rack_Point_Model_Source_Mode
{
    const RECEIVE_MODE_PRODUCT  = 1;
    const RECEIVE_MODE_ORDER    = 2;
    
    public function toOptionArray()
    {
        return array(
           self::RECEIVE_MODE_PRODUCT => Mage::helper('rackpoint')->__('Per product'),
           self::RECEIVE_MODE_ORDER   => Mage::helper('rackpoint')->__('Per order'),
        );
    }
}