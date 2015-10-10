<?php

class Rack_Point_Block_Adminhtml_Customer_View
 extends Mage_Adminhtml_Block_Template
 implements Mage_Adminhtml_Block_Widget_Tab_Interface
{

    protected $_customer;

    public function getCustomer()
    {
        if (!$this->_customer) {
            $this->_customer = Mage::registry('current_customer');
        }
        return $this->_customer;
    }

    public function getStoreId()
    {
        return $this->getCustomer()->getStoreId();
    }

    public function getTabLabel()
    {
        return Mage::helper('rackpoint')->__('Customer Points');
    }

    public function getTabTitle()
    {
        return Mage::helper('rackpoint')->__('Customer Points');
    }

    public function canShowTab()
    {
        if (Mage::registry('current_customer')->getId()) {
            return true;
        }
        return false;
    }

    public function isHidden()
    {
        if (Mage::registry('current_customer')->getId()) {
            return false;
        }
        return true;
    }

}