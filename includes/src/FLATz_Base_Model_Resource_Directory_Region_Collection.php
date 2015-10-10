<?php

/**
 * @license http://opensource.org/licenses/osl-3.0.php OSL-3.0
 * @author Yoshihisa AMAKATA <amakata@flatz.jp>
 * @copyright (c) 2013, FLATz Inc.
 */
class FLATz_Base_Model_Resource_Directory_Region_Collection extends Mage_Directory_Model_Resource_Region_Collection {

    /**
     * コンストラクタ
     */
    protected function _construct() {
        $this->_init('directory/region');

        $this->_countryTable = $this->getTable('directory/country');
        $this->_regionNameTable = $this->getTable('directory/country_region_name');

        if (Mage::getModel('core/locale')->getLocaleCode() !== 'ja_JP') {
            $this->addOrder('name', Varien_Data_Collection::SORT_ORDER_ASC);
            $this->addOrder('default_name', Varien_Data_Collection::SORT_ORDER_ASC);
        }
    }

    /**
     * Convert collection items to select options array
     *
     * @return array
     */
    public function toOptionArray() {
        if (Mage::getModel('core/locale')->getLocaleCode() !== 'ja_JP') {
            $options = $this->_toOptionArray('region_id', 'default_name', array('title' => 'default_name'));
        } else {
            $options = $this->_toOptionArray('region_id', 'name', array('title' => 'default_name'));
        }
        if (count($options) > 0) {
            array_unshift($options, array(
                'title ' => null,
                'value' => '0',
                'label' => Mage::helper('directory')->__('-- Please select --')
            ));
        }
        return $options;
    }

}
