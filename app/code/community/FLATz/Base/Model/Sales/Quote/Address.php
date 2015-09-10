<?php

/**
 * @license http://opensource.org/licenses/osl-3.0.php OSL-3.0
 * @author Yoshihisa AMAKATA <amakata@flatz.jp>
 * @copyright (c) 2013, FLATz Inc.
 */
class FLATz_Base_Model_Sales_Quote_Address extends Mage_Sales_Model_Quote_Address {

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

    /**
     * 
     * @return boolean|array
     */
    public function validate() {
        $helper = Mage::helper('flatz_base/validator');
        $this->implodeStreetAddress();
        if ($this->getConfigData(FLATz_Base_Helper_Validator::ADDRESS_FULL_WIDTH_ONLY)) {
            $helper->convertStreetToFullWidth($this);
            $helper->convertCityToFullWidth($this);
        }

        $errors = parent::validate();

        if ($errors === true) {
            $errors = array();
        }

        $helper->validateName($this, $errors);

        if (Mage::getStoreConfig('flatz_base_japanese/name/usekana')) {
            $helper->validateKana($this, $errors);
        }

        $helper->validateAddress($this, $errors);
        $helper->validatePostcode($this, $errors);
        $helper->validateTel($this, $errors);
        $helper->validateFax($this, $errors);

        Mage::dispatchEvent('validator_sales_quote_address_validate', array('address' => $this, 'error' => $errors));

        if (empty($errors) || $this->getShouldIgnoreValidation()) {
            return true;
        }
        return $errors;
    }

    /**
     * 
     * @param string $key
     * @return mixed
     */
    public function getConfigData($key) {
        return Mage::getStoreConfig($key);
    }

}
