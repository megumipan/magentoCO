<?php

/**
 * @license http://opensource.org/licenses/osl-3.0.php OSL-3.0
 * @author Yoshihisa AMAKATA <amakata@flatz.jp>
 * @copyright (c) 2013, FLATz Inc.
 */
class FLATz_Base_Model_Sales_Order extends Mage_Sales_Model_Order {

    /**
     * 
     * @return string
     */
    public function getCustomerName() {
        if (!Mage::getStoreConfig('flatz_base_japanese/name/enablejp')) {
            return parent::getCustomerName();
        }
        if ($this->getCustomerFirstname()) {
            $customerName = $this->getCustomerLastname() . ' ' . $this->getCustomerFirstname();
        } else {
            $customerName = Mage::helper('sales')->__('Guest');
        }
        return $customerName;
    }

}
