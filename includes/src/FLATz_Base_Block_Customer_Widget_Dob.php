<?php

/**
 * @license http://opensource.org/licenses/osl-3.0.php OSL-3.0
 * @author Yoshihisa AMAKATA <amakata@flatz.jp>
 * @copyright (c) 2013, FLATz Inc.
 */
class FLATz_Base_Block_Customer_Widget_Dob extends Mage_Customer_Block_Widget_Dob {

    /**
     * コンストラクタ
     */
    public function _construct() {
        parent::_construct();

        // default template location
        if (Mage::getStoreConfig('flatz_base_japanese/additional/useselectfordob')) {
            $this->setTemplate('flatz_base/customer/widget/dob.phtml');
        }
    }

}