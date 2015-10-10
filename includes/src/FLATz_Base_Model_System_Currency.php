<?php

/**
 * @license http://opensource.org/licenses/osl-3.0.php OSL-3.0
 * @author Yoshihisa AMAKATA <amakata@flatz.jp>
 * @copyright (c) 2013, FLATz Inc.
 */
class FLATz_Base_Model_System_Currency {

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray() {
        return array(
            array('value' => Zend_Currency::RIGHT, 'label' => Mage::helper('flatz_base')->__('Right and use Kanji')),
            array('value' => Zend_Currency::LEFT, 'label' => Mage::helper('flatz_base')->__('Left and use symbol')),
        );
    }

}
