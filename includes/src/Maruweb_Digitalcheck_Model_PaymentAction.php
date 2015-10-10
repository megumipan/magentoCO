<?php
class Maruweb_Digitalcheck_Model_Paymentaction
{
    public function toOptionArray()
    {
        return array(
                array(
                    'value' => "authorize", 
                    'label' => Mage::helper('digitalcheck')->__('Authorize Only')
                    ),
                array(
                    'value' => "authorize_capture", 
                    'label' => Mage::helper('digitalcheck')->__('Authorize and Capture')
                    ),
                );
    }
}

