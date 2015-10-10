<?php

class Rack_Point_Model_Point_History extends Mage_Core_Model_Abstract
{
    const ACTION_BY_INVOICE                = 'invoiced';
    const ACTION_BY_RECEIVED_REFUNDED      = 'reveived_refunded';
    const ACTION_BY_USED_REFUNDED          = 'used_refunded';
    const ACTION_BY_PLACE_ORDER            = 'place_order';
    const ACTION_BY_REGISTER               = 'register';
    const ACTION_BY_WRITE_REVIEW           = 'write_review';
    const ACTION_BY_NEWSLETTER             = 'newsletter_subscribed';
    const ACTION_BY_CANCEL_ORDER           = 'cancel_order';
    const ACTION_BY_ADMIN                  = 'moderation';
    const ACTION_IMPORT                    = 'import';
    
    protected function _construct() 
    {
        $this->_init('rackpoint/point_history');
    }
    
    protected function _beforeSave() 
    {
        if (!$this->getPointRate()) {
            $this->setPointRate(Mage::helper('rackpoint')->getPointRate());
        }
    }
    
    public function getRateDescription()
    {
        $rate = $this->getRate();
        
        if ($rate == null) {
            $rate = Mage::helper('rackpoint')->getPointRate();
        }
        
        return Mage::helper('rackpoint')->__('%s point(s) = 1 %s', $rate, Mage::app()->getBaseCurrencyCode());
    }
    
    public function getCreatedAtFormated()
    {
        $date = $this->getCreatedAt();
        
        $newDate = Mage::app()->getLocale()->date($date, Varien_Date::DATETIME_INTERNAL_FORMAT);
        
        return $newDate->toString('YYYY-MM-dd HH:mm');
    }
    
    public function getCurrency($formated = false, $includeStore = false)
    {
        $rate = $this->getRate();
        if ($rate == 0) {
            $currency = $this->getPoint();
        } else {
            $currency = $this->getPoint() / $rate;
        }
        if ($includeStore == true) {
            $convertCurrency = Mage::helper('rackpoint')->convertCurrency($currency, Mage::app()->getStore($this->getStoreId())->getDefaultCurrencyCode());
        }
        if ($formated == true) {
            $currency = Mage::app()->getStore(0)->formatPrice($currency);
            if ($includeStore) {
                $convertCurrency =  Mage::app()->getStore($this->getStoreId())->formatPrice($convertCurrency);
            }
        }

        if ($includeStore && $currency != $convertCurrency) {
            return $currency . ' (' . $convertCurrency . ')';
        }

        return $currency;
    }
}