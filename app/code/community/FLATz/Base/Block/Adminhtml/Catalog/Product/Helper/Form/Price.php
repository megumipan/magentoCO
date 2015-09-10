<?php

/**
 * @license http://opensource.org/licenses/osl-3.0.php OSL-3.0
 * @author Yoshihisa AMAKATA <amakata@flatz.jp>
 * @copyright (c) 2013, FLATz Inc.
 */

class FLATz_Base_Block_Adminhtml_Catalog_Product_Helper_Form_Price extends Mage_Adminhtml_Block_Catalog_Product_Helper_Form_Price {
    /**
     * 
     * @param type $index
     * @return string|null
     */
    public function getEscapedValue($index = null) {
        $value = $this->getValue();

        if (!is_numeric($value)) {
            return null;
        }

        $store = 0;
        if (Mage::app()->getRequest()->getParam('store', null)) {
            $store = Mage::app()->getRequest()->getParam('store', null);
        }

        if (Mage::app()->getStore($store)->getBaseCurrencyCode() == 'JPY') {
            return number_format($value, 0, null, '');
        }
        return number_format($value, 2, null, '');
    }

}
