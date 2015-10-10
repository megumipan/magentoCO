<?php

class Rack_Point_Block_Customer_Account extends Mage_Core_Block_Template
{
    public function getCurrentBalance()
    {
        $balance = Mage::getSingleton('rackpoint/point_balance')->getBalanceOfCurrentCustomer(true);
        
        return $balance;
    }
    
    public function formatPoint($balance)
    {
        return $this->__('%s points (%s)', $balance->getPoint(), $this->formatCurrency($balance->getCurrency()));
    }
    
    public function formatCurrency($currency)
    {
        $currencyObj = Mage::getModel('directory/currency')->load(Mage::app()->getBaseCurrencyCode());
        
        return $currencyObj->formatPrecision($currency, 0, array(), true, false);
    }
    
    public function getRateDescription()
    {
        $rate = Mage::helper('rackpoint')->getPointRate();
        
        return $this->__('%d point = 1%s', $rate, Mage::app()->getBaseCurrencyCode());
    }
}