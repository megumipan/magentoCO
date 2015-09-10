<?php

/**
 * @license http://opensource.org/licenses/osl-3.0.php OSL-3.0
 * @author Yoshihisa AMAKATA <amakata@flatz.jp>
 * @copyright (c) 2013, FLATz Inc.
 */
class FLATz_Base_Block_Adminhtml_Newsletter_Subscriber_Grid extends Mage_Adminhtml_Block_Newsletter_Subscriber_Grid {

    /**
     * コンストラクタ
     */
    public function __construct() {
        parent::__construct();
    }

    /**
     * 
     * @return \FLATz_Base_Block_Adminhtml_Newsletter_Subscriber_Grid
     */
    protected function _prepareColumns() {
        parent::_prepareColumns();
        if (Mage::app()->getLocale()->getLocaleCode() != 'ja_JP') {
            return $this;
        }
        $this->_columns = array();
        $this->addColumn('subscriber_id', array(
            'header' => Mage::helper('newsletter')->__('ID'),
            'index' => 'subscriber_id'
        ));

        $this->addColumn('email', array(
            'header' => Mage::helper('newsletter')->__('Email'),
            'index' => 'subscriber_email'
        ));

        $this->addColumn('type', array(
            'header' => Mage::helper('newsletter')->__('Type'),
            'index' => 'type',
            'type' => 'options',
            'options' => array(
                1 => Mage::helper('newsletter')->__('Guest'),
                2 => Mage::helper('newsletter')->__('Customer')
            )
        ));

        $this->addColumn('lastname', array(
            'header' => Mage::helper('newsletter')->__('Customer Last Name'),
            'index' => 'customer_lastname',
            'default' => '----'
        ));

        $this->addColumn('firstname', array(
            'header' => Mage::helper('newsletter')->__('Customer First Name'),
            'index' => 'customer_firstname',
            'default' => '----'
        ));

        $this->addColumn('status', array(
            'header' => Mage::helper('newsletter')->__('Status'),
            'index' => 'subscriber_status',
            'type' => 'options',
            'options' => array(
                Mage_Newsletter_Model_Subscriber::STATUS_NOT_ACTIVE => Mage::helper('newsletter')->__('Not Activated'),
                Mage_Newsletter_Model_Subscriber::STATUS_SUBSCRIBED => Mage::helper('newsletter')->__('Subscribed'),
                Mage_Newsletter_Model_Subscriber::STATUS_UNSUBSCRIBED => Mage::helper('newsletter')->__('Unsubscribed'),
            )
        ));

        $this->addColumn('website', array(
            'header' => Mage::helper('newsletter')->__('Website'),
            'index' => 'website_id',
            'type' => 'options',
            'options' => $this->_getWebsiteOptions()
        ));

        $this->addColumn('group', array(
            'header' => Mage::helper('newsletter')->__('Store'),
            'index' => 'group_id',
            'type' => 'options',
            'options' => $this->_getStoreGroupOptions()
        ));

        $this->addColumn('store', array(
            'header' => Mage::helper('newsletter')->__('Store View'),
            'index' => 'store_id',
            'type' => 'options',
            'options' => $this->_getStoreOptions()
        ));

        $this->addExportType('*/*/exportCsv', Mage::helper('customer')->__('CSV'));
        $this->addExportType('*/*/exportXml', Mage::helper('customer')->__('XML'));
        return $this;
    }

}