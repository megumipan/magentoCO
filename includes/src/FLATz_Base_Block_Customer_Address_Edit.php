<?php

/**
 * @license http://opensource.org/licenses/osl-3.0.php OSL-3.0
 * @author Yoshihisa AMAKATA <amakata@flatz.jp>
 * @copyright (c) 2013, FLATz Inc.
 */
class FLATz_Base_Block_Customer_Address_Edit extends Mage_Customer_Block_Address_Edit {

    /**
     * 
     * @return \FLATz_Base_Block_Customer_Address_Edit
     */
    protected function _prepareLayout() {
        parent::_prepareLayout();
        $this->_address = Mage::getModel('customer/address');

        // Init address object
        if ($id = $this->getRequest()->getParam('id')) {
            $this->_address->load($id);
            if ($this->_address->getCustomerId() != Mage::getSingleton('customer/session')->getCustomerId()) {
                $this->_address->setData(array());
            }
        }

        if (!$this->_address->getId()) {
            $this->_address->setPrefix($this->getCustomer()->getPrefix())
                    ->setFirstname($this->getCustomer()->getFirstname())
                    ->setMiddlename($this->getCustomer()->getMiddlename())
                    ->setLastname($this->getCustomer()->getLastname())
                    ->setFirstnamekana($this->getCustomer()->getFirstnamekana())
                    ->setLastnamekana($this->getCustomer()->getLastnamekana())
                    ->setSuffix($this->getCustomer()->getSuffix());
        }

        if ($headBlock = $this->getLayout()->getBlock('head')) {
            $headBlock->setTitle($this->getTitle());
        }
        if ($postedData = Mage::getSingleton('customer/session')->getAddressFormData(true)) {
            $this->_address->setData($postedData);
        }
        return $this;
    }

}