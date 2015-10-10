<?php

/**
 * @license http://opensource.org/licenses/osl-3.0.php OSL-3.0
 * @author Yoshihisa AMAKATA <amakata@flatz.jp>
 * @copyright (c) 2013, FLATz Inc.
 */
class FLATz_Base_Model_Store extends Mage_Core_Model_Store {

    /**
     * 
     * @param double $price
     * @return double
     */
    public function roundPrice($price)
    {
        if (Mage::helper('flatz_base')->canUseJpy()) {
            if(!Mage::helper('tax')->priceIncludesTax($this->getWebsiteId()) &&
                (Mage::helper('tax')->getCalculationAgorithm($this->getWebsiteId()) != Mage_Tax_Model_Calculation::CALC_ROW_BASE)) {

                $method = Mage::getStoreConfig('tax/calculation/round');
                return $method($price);
            }
            return round($price, 0);
        }
        return parent::roundPrice($price);
    }
}
