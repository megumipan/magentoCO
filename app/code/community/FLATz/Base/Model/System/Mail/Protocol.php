<?php

/**
 * @license http://opensource.org/licenses/osl-3.0.php OSL-3.0
 * @author Yoshihisa AMAKATA <amakata@flatz.jp>
 * @copyright (c) 2013, FLATz Inc.
 */

/**
 * Mail プロトコル
 */
class FLATz_Base_Model_System_Mail_Protocol {

    /**
     * オプションの配列
     *
     * @return array
     */
    public function toOptionArray() {
        return array(
            array('value' => '2', 'label' => Mage::helper('flatz_base')->__('TLS')),
            array('value' => '1', 'label' => Mage::helper('flatz_base')->__('SSL')),
            array('value' => '0', 'label' => Mage::helper('flatz_base')->__('Normal')),
        );
    }

}