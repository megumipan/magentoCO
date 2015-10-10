<?php

/**
 * @license http://opensource.org/licenses/osl-3.0.php OSL-3.0
 * @author Yoshihisa AMAKATA <amakata@flatz.jp>
 * @copyright (c) 2013, FLATz Inc.
 */
class FLATz_Base_Block_Js extends Mage_Core_Block_Template {

    /**
     * 
     * @return string
     */
    protected function getJsConfig() {
        $html = '';

        if (Mage::helper('flatz_base')->canUseJpy()) {
            $html = 'var jpCorePrecision = 0;';
            $html.= 'var method ="' . Mage::getStoreConfig('tax/calculation/round') . '";';
        } else {
            $html = 'var jpCorePrecision = 2;';
        }


        return $html;
    }

}
