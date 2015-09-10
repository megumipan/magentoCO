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

class IrvineSystems_Sagawa_Block_Adminhtml_Mails_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
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

		// Add Customer Section Tab
        $this->addTab('customer_section', array(
            'label'     => $helper->__('Customer Information'),
            'title'     => $helper->__('Customer Information'),
            'content'   => $this->getLayout()->createBlock('sagawa/adminhtml_mails_edit_tab_customer')->toHtml(),
        ));

		// Return Parent construction
        return parent::_beforeToHtml();
    }
}