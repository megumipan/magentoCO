<?php

/**
 * @license http://opensource.org/licenses/osl-3.0.php OSL-3.0
 * @author Yoshihisa AMAKATA <amakata@flatz.jp>
 * @copyright (c) 2013, FLATz Inc.
 */
class FLATz_Base_Model_System_Round {

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray() {
        return array(
            array('value' => 'ceil', 'label' => Mage::helper('flatz_base')->__('Ceil')),
            array('value' => 'floor', 'label' => Mage::helper('flatz_base')->__('Floor')),
            array('value' => 'round', 'label' => Mage::helper('flatz_base')->__('Round')),
        );
    }

}
