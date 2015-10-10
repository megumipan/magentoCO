<?php

/**
 * @license http://opensource.org/licenses/osl-3.0.php OSL-3.0
 * @author Yoshihisa AMAKATA <amakata@flatz.jp>
 * @copyright (c) 2013, FLATz Inc.
 */
class FLATz_Base_Block_Adminhtml_Catalog_Product_Edit_Tab_Tag_Customer extends Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Tag_Customer {

    /**
     * 
     * @return \FLATz_Base_Block_Adminhtml_Catalog_Product_Edit_Tab_Tag_Customer
     */
    protected function _prepareColumns() {
        if (Mage::app()->getLocale()->getLocaleCode() == 'ja_JP') {
            $this->addColumn('lastname', array(
                'header' => Mage::helper('catalog')->__('Last Name'),
                'index' => 'lastname',
            ));
            
            $this->addColumn('firstname', array(
                'header' => Mage::helper('catalog')->__('First Name'),
                'index' => 'firstname',
            ));

            $this->addColumn('email', array(
                'header' => Mage::helper('catalog')->__('Email'),
                'index' => 'email',
            ));

            $this->addColumn('name', array(
                'header' => Mage::helper('catalog')->__('Tag Name'),
                'index' => 'name',
            ));
            
            $this->sortColumnsByOrder();
            
            return $this;
        } else {
            return parent::_prepareColumns();
        }
    }

}