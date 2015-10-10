<?php
/*
 * Irvine Systems Shipping Japan Sgw
 *
 * NOTICE OF LICENSE
 * This source file is subject to the Revised BSD License which is bundled with
 * this package in the files LICENSE.txt and LICENSE.html.
 *
 * @category	Magento Shipping Extension
 * @package		IrvineSystems_Sagawa
 * @author Irvine Systems, Inc.
 * @copyright Copyright (c) 2015, Irvine Systems, Inc. (http://irvinesystems.co.jp/)
 * @license     Revised BSD License
 */

class IrvineSystems_Sagawa_Block_Adminhtml_Parcels_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
   /**
    * Block constructor, prepare Selectable Tabs
    *
    */
    public function __construct()
    {
        // Construct Parent Tabs
        parent::__construct();

        // Construct Tabs Proprieties
        $this->setId('slip_id');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('sagawa')->__('Slips Information Data'));
    }

   /**
    * Prepare Tabs for HTML
    *
    * @return Mage_Adminhtml_Block_Widget_Tabs
    */
    protected function _beforeToHtml()
    {
        // Set Helper Model
        $helper = Mage::helper('sagawa');

		// Add Order Section Tab
        $this->addTab('order_section', array(
            'label'     => $helper->__('Order Information'),
            'title'     => $helper->__('Order Information'),
            'content'   => $this->getLayout()->createBlock('sagawa/adminhtml_parcels_edit_tab_order')->toHtml(),
            'active'    => true
        ));

		// Add Delivery Section Tab
        $this->addTab('delivery_section', array(
            'label'     => $helper->__('Delivery Information'),
            'title'     => $helper->__('Delivery Information'),
            'content'   => $this->getLayout()->createBlock('sagawa/adminhtml_parcels_edit_tab_delivery')->toHtml(),
        ));

		// Add Customer Section Tab
        $this->addTab('customer_section', array(
            'label'     => $helper->__('Customer Information'),
            'title'     => $helper->__('Customer Information'),
            'content'   => $this->getLayout()->createBlock('sagawa/adminhtml_parcels_edit_tab_customer')->toHtml(),
        ));

		// Add Store Section Tab
        $this->addTab('store_section', array(
            'label'     => $helper->__('Store Information'),
            'title'     => $helper->__('Store Information'),
            'content'   => $this->getLayout()->createBlock('sagawa/adminhtml_parcels_edit_tab_store')->toHtml(),
        ));

		// Return Parent construction
        return parent::_beforeToHtml();
    }
}