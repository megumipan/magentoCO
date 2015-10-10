<?php

class Rack_Point_Block_Checkout_Cart_Info extends Mage_Core_Block_Template
{
    public function getPointInfo()
    {
        $receivePointInfo = $this->getQuote()->getPointCalculationInfo();
        $receivePoint = 0;
        if (is_array($receivePointInfo)) {
            foreach ($receivePointInfo as $info) {
                $receivePoint += $info->getPointReceived();
            }
        } else {
            $receivePoint = 0;
        }
        $receivePoint = Mage::helper('rackpoint')->roundPoint($receivePoint);
        $receiveCurrency = Mage::helper('rackpoint')->point2Currency($receivePoint);
        if ($receivePoint > 0) {
            if (Mage::getSingleton('customer/session')->getCustomer()->getId()) {
                return $this->__('Checkout now to get %s points (%s) for this order.', $receivePoint, $this->formatPrice($receiveCurrency));
            } else {
                return $this->__('Login or register new account and checkout to get %s point (%s) for this order.', $receivePoint, $this->formatPrice($receiveCurrency));
            }
        } else {
            return '';
        }
    }

    /**
     * Return current quote object
     *
     * @return Mage_Sales_Model_Quote
     */
    public function getQuote()
    {
        return Mage::getSingleton('checkout/session')->getQuote();
    }

    public function formatPrice($price)
    {
        return $this->getQuote()->getStore()->formatPrice($price);
    }

    public function _toHtml()
    {
        if (Mage::helper('rackpoint')->isEnabled() == false && $this->getQuote()->getPointReceived() > 0) {
            return '';
        }

        return parent::_toHtml();
    }
}