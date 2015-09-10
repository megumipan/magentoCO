<?php

/**
 * @license http://opensource.org/licenses/osl-3.0.php OSL-3.0
 * @author Yoshihisa AMAKATA <amakata@flatz.jp>
 * @copyright (c) 2013, FLATz Inc.
 */
class FLATz_Base_Block_Adminhtml_Permissions_User_Grid extends Mage_Adminhtml_Block_Permissions_User_Grid {

    /**
     * コンストラクタ
     */
    public function __construct() {
        parent::__construct();
    }

    /**
     * 
     * @return \FLATz_Base_Block_Adminhtml_Permissions_User_Grid
     */
    protected function _prepareColumns() {
        parent::_prepareColumns();
        if (Mage::app()->getLocale()->getLocaleCode() != 'ja_JP') {
            return $this;
        }
        $this->_columns = array();
        $this->addColumn('user_id', array(
            'header' => Mage::helper('adminhtml')->__('ID'),
            'width' => 5,
            'align' => 'right',
            'sortable' => true,
            'index' => 'user_id'
        ));

        $this->addColumn('username', array(
            'header' => Mage::helper('adminhtml')->__('User Name'),
            'index' => 'username'
        ));

        $this->addColumn('lastname', array(
            'header' => Mage::helper('adminhtml')->__('Last Name'),
            'index' => 'lastname'
        ));

        $this->addColumn('firstname', array(
            'header' => Mage::helper('adminhtml')->__('First Name'),
            'index' => 'firstname'
        ));

        $this->addColumn('email', array(
            'header' => Mage::helper('adminhtml')->__('Email'),
            'width' => 40,
            'align' => 'left',
            'index' => 'email'
        ));

        $this->addColumn('is_active', array(
            'header' => Mage::helper('adminhtml')->__('Status'),
            'index' => 'is_active',
            'type' => 'options',
            'options' => array('1' => Mage::helper('adminhtml')->__('Active'), '0' => Mage::helper('adminhtml')->__('Inactive')),
        ));

        return $this;
    }

}