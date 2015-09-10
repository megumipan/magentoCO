<?php

/**
 * @license http://opensource.org/licenses/osl-3.0.php OSL-3.0
 * @author Yoshihisa AMAKATA <amakata@flatz.jp>
 * @copyright (c) 2013, FLATz Inc.
 */
class FLATz_Base_Model_Currency extends Mage_Directory_Model_Currency {

    /**
     * 
     * @param number $price
     * @param number $precision
     * @param array() $options
     * @param boolean $includeContainer
     * @param boolean $addBrackets
     * @return string
     */
    public function formatPrecision($price, $precision, $options = array(), $includeContainer = true, $addBrackets = false) {
        if (!isset($options['precision'])) {
            $options['precision'] = $precision;
        }
        if ($this->getCode() === 'JPY' && Mage::helper('flatz_base')->canUseJpy()) {
            $options = Mage::helper('flatz_base')->getOptions(array());
        }
        if ($includeContainer) {
            return '<span class="price">' . ($addBrackets ? '[' : '') . $this->formatTxt($price, $options) . ($addBrackets ? ']' : '') . '</span>';
        }
        return $this->formatTxt($price, $options);
    }
    
    public function formatTxt($price, $options = array()) {
        $options = Mage::helper('flatz_base')->getOptions($options);
        return parent::formatTxt($price, $options);
    }
}
