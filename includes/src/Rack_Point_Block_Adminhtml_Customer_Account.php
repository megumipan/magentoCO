<?php

class Rack_Point_Block_Adminhtml_Customer_Account extends Mage_Adminhtml_Block_Template
{
    protected $_customer;

    public function getCustomer()
    {
        if (!$this->_customer) {
            $this->_customer = Mage::registry('current_customer');
        }
        
        return $this->_customer;
    }
    
    public function getCurrentBalance()
    {
        $balance = Mage::getModel('rackpoint/point_balance')->getBalanceOfCurrentCustomer(true, $this->getCustomer()->getId(), $this->getCustomer()->getWebsiteId());
        
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
    
    public function getCurrencyFormated($balance)
    {
        $currency = Mage::helper('rackpoint')->point2Currency($balance->getBalance());
        
        return $this->formatCurrency($currency);
    }

    public function getRateDescription()
    {
        $rate = Mage::helper('rackpoint')->getPointRate();
        
        return $this->__('%d point = 1%s', $rate, Mage::app()->getBaseCurrencyCode());
    }
    
    public function getBalanceCollection()
    {
        $collection = Mage::getResourceModel('rackpoint/point_balance_collection');
        $collection->addWebsiteInfo()->addCustomerFilter($this->getCustomer()->getId());
        
        return $collection;
    }
}