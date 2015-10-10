<?php

class FLATz_Base_Block_Customer_Widget_Gender extends Mage_Customer_Block_Widget_Gender {

    public function _construct() {
        parent::_construct();
        if (Mage::getStoreConfig('flatz_base_japanese/additional/useradioforgender')) {
            $this->setTemplate('flatz_base/customer/widget/gender.phtml');
        }
    }

}
