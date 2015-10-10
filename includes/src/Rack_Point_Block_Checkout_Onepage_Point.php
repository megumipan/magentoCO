<?php

class Rack_Point_Block_Checkout_Onepage_Point extends Mage_Core_Block_Template
{
    /**
     * Get base grand total of current quote
     * 
     * @return float
     */
    public function getGrandTotal()
    {
        $quote = $this->getQuote();
        
        return $quote->getBaseGrandTotal();
    }
    
    /**
     * Get current used point
     * 
     * @return int
     */
    public function getUsedPoint()
    {
        $session = Mage::getSingleton('checkout/session');
        if ($session->getPointUsed()) {
            return $session->getPointUsed();
        } else {
            return 0;
        }
    }
    
    /**
     * Get current point currency.
     * 
     * @return float
     */
    public function getUsedPointCurrency()
    {
        $usedPoint = $this->getUsedPoint();
        $money = Mage::helper('rackpoint')->point2Currency($usedPoint);
        
        return $money;
    }

    /**
     * Get current balance
     * 
     * @return Varien_Object 
     */
    public function getCurrentBalance()
    {
        $balance = Mage::getSingleton('rackpoint/point_balance');
        $result = $balance->getBalanceOfCurrentCustomer(true);
        
        return $result;
    }

    /**
     * Get current quote
     * 
     * @return Mage_Sales_Model_Quote
     */
    public function getQuote()
    {
        $quote = Mage::getSingleton('checkout/session')->getQuote();
        
        return $quote;
    }
    
    public function formatCurrency($currency)
    {
        return Mage::app()->getStore()->formatPrice($currency);
    }
}