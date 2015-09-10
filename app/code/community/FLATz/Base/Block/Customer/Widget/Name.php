<?php

/**
 * @license http://opensource.org/licenses/osl-3.0.php OSL-3.0
 * @author Yoshihisa AMAKATA <amakata@flatz.jp>
 * @copyright (c) 2013, FLATz Inc.
 */
class FLATz_Base_Block_Customer_Widget_Name extends Mage_Customer_Block_Widget_Name {

    /**
     * コンストラクタ
     */
    public function _construct() {
        parent::_construct();

        // default template location
        if (Mage::getStoreConfig('flatz_base_japanese/name/enablejp')) {
            $this->setTemplate('flatz_base/customer/widget/name_jp.phtml');
        } else {
            $this->setTemplate('flatz_base/customer/widget/name_en.phtml');
        }
    }

}