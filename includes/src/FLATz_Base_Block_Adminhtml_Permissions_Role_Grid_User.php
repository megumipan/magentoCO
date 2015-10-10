<?php

/**
 * @license http://opensource.org/licenses/osl-3.0.php OSL-3.0
 * @author Yoshihisa AMAKATA <amakata@flatz.jp>
 * @copyright (c) 2013, FLATz Inc.
 */
class FLATz_Base_Block_Adminhtml_Permissions_Role_Grid_User extends Mage_Adminhtml_Block_Permissions_Role_Grid_User {

    /**
     * コンストラクタ
     */
    public function __construct() {
        parent::__construct();
    }

    /**
     * 
     * @return \FLATz_Base_Block_Adminhtml_Permissions_Role_Grid_User
     */
    protected function _prepareColumns() {
        parent::_prepareColumns();
        if (Mage::app()->getLocale()->getLocaleCode() != 'ja_JP') {
            return $this;
        }
        $this->_columns = array();
        $this->addColumn('in_role_users', array(
            'header_css_class' => 'a-center',
            'type' => 'checkbox',
            'name' => 'in_role_users',
            'values' => $this->_getUsers(),
            'align' => 'center',
            'index' => 'user_id'
        ));

        $this->addColumn('role_user_id', array(
            'header' => Mage::helper('adminhtml')->__('User ID'),
            'width' => 5,
            'align' => 'left',
            'sortable' => true,
            'index' => 'user_id'
        ));

        $this->addColumn('role_user_username', array(
            'header' => Mage::helper('adminhtml')->__('User Name'),
            'align' => 'left',
            'index' => 'username'
        ));

        $this->addColumn('role_user_lastname', array(
            'header' => Mage::helper('adminhtml')->__('Last Name'),
            'align' => 'left',
            'index' => 'lastname'
        ));

        $this->addColumn('role_user_firstname', array(
            'header' => Mage::helper('adminhtml')->__('First Name'),
            'align' => 'left',
            'index' => 'firstname'
        ));

        $this->addColumn('role_user_email', array(
            'header' => Mage::helper('adminhtml')->__('Email'),
            'width' => 40,
            'align' => 'left',
            'index' => 'email'
        ));

        $this->addColumn('role_user_is_active', array(
            'header' => Mage::helper('adminhtml')->__('Status'),
            'index' => 'is_active',
            'align' => 'left',
            'type' => 'options',
            'options' => array('1' => Mage::helper('adminhtml')->__('Active'), '0' => Mage::helper('adminhtml')->__('Inactive')),
        ));
        return $this;
    }

}