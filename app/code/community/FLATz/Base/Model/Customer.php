<?php

/**
 * @license http://opensource.org/licenses/osl-3.0.php OSL-3.0
 * @author Yoshihisa AMAKATA <amakata@flatz.jp>
 * @copyright (c) 2013, FLATz Inc.
 */
class FLATz_Base_Model_Customer extends Mage_Customer_Model_Customer {

    /**
     * 名前の取得
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
        $errors = parent::validate();

        if ($errors === true) {
            $errors = array();
        }

        $customerHelper = Mage::helper('flatz_base/validator');

        $customerHelper->validateName($this, $errors);

        if (Mage::getStoreConfig('flatz_base_japanese/name/usekana')) {
            $customerHelper->validateKana($this, $errors);
        }

        Mage::dispatchEvent('validator_customer_validate', array('customer' => $this, 'error' => $errors));

        if (empty($errors)) {
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

    /**
     * 
     * @param string $name
     * @return boolean
     */
    public function isModuleInstalled($name) {
        $modules = array_keys((array) Mage::getConfig()->getNode('modules')->children());
        if (in_array($name, $modules)) {
            return true;
        }
        return false;
    }

}
