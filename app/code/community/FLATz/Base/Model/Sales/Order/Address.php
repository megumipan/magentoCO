<?php

/**
 * @license http://opensource.org/licenses/osl-3.0.php OSL-3.0
 * @author Yoshihisa AMAKATA <amakata@flatz.jp>
 * @copyright (c) 2013, FLATz Inc.
 */
class FLATz_Base_Model_Sales_Order_Address extends Mage_Sales_Model_Order_Address {

    /**
     * 
     * @return string
     */
    public function getName() {
        if (!Mage::getStoreConfig('flatz_base_japanese/name/enablejp')) {
            return parent::getName();
        }
        $name = '';
        $helper = Mage::helper('customer/address');
        if ($helper->canShowConfig('prefix_show') && $this->getPrefix()) {
            $name .= $this->getPrefix() . ' ';
        }
        $name .= $this->getLastname();
        if ($helper->canShowConfig('middlename_show') && $this->getMiddlename()) {
            $name .= ' ' . $this->getMiddlename();
        }
        $name .= ' ' . $this->getFirstname();
        if ($helper->canShowConfig('suffix_show') && $this->getSuffix()) {
            $name .= ' ' . $this->getSuffix();
        }
        return $name;
    }

}
