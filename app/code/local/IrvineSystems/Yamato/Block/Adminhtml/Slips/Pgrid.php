<?php
/*
 * Irvine Systems Shipping Japan Ymt
 *
 * NOTICE OF LICENSE
 * This source file is subject to the Revised BSD License which is bundled with
 * this package in the files LICENSE.txt and LICENSE.html.
 *
 * @category	Magento Shipping Extension
 * @package		IrvineSystems_Yamato
 * @author Irvine Systems, Inc.
 * @copyright Copyright (c) 2015, Irvine Systems, Inc. (http://irvinesystems.co.jp/)
 * @license     Revised BSD License
 */

class IrvineSystems_Yamato_Block_Adminhtml_Slips_Pgrid extends Mage_Adminhtml_Block_Widget_Grid
{
   /**
    * Block constructor, prepare Grid params
    *
    */
	public function __construct()
	{
		// Construct Parent Grid
		parent::__construct();

        // Set Grid Proprietaries
		$this->setId('slipsGrid');
		//$this->setDefaultSort('slip_id');
		$this->setDefaultDir('DESC');
		$this->setSaveParametersInSession(true);
	}

	/* 
	 * Prepare Data Collection for rules grid
	 * 
     * @return Mage_Adminhtml_Block_Widget_Grid
	*/
	protected function _prepareCollection()
	{
        // Set Default Status
		$status = 'processing';
		// Prepare data Collection
		$collection = Mage::getResourceModel('yamato/slips_collection')->addOrderStatusFilter($status);
        // Set the Collection
		$this->setCollection($collection);
        // Prepare Parent Collection
		return parent::_prepareCollection();
	}

    /**
     * Prepare columns for rules grid
     *
     * @return Mage_Adminhtml_Block_Widget_Grid
     */
	protected function _prepareColumns()
	{
        // Set Points Model
		$model = Mage::getModel('yamato/slips');
        // Set Helper Model
        $helper = Mage::helper('yamato');

		/*
		// Set Main Id Column
		$this->addColumn('slip_id', array(
			'header'	=> $helper->__('ID'),
			'align'		=>'right',
			'width'		=> '50px',
			'index'		=> 'slip_id',
		));

		// Set Order Number Column
		$this->addColumn('order_id', array(
			'header'	=> $helper->__('Order Number'),
			'align'		=>'right',
			'index'		=> 'order_id',
		));

		// Set Tracking Number Column
		$this->addColumn('tracking_number', array(
			'header'	=> $helper->__('Tracking Number'),
			'align'		=>'right',
			'index'		=> 'tracking_number',
		));
		*/
		// Set Customer Number Column
		$this->addColumn('customer_number', array(
			'header'	=> $helper->__('Customer Number'),
			'align'		=>'right',
			'index'		=> 'customer_number',
		));
		// Set Delivery Mode Column
		$this->addColumn('delivary_mode', array(
			'header'	=> $helper->__('Delivery Mode'),
			'align'		=>'right',
			'index'		=> 'delivary_mode',
		));
		// Set Cool Type Column
		$this->addColumn('cool_type', array(
			'header'	=> $helper->__('Cool Type'),
			'align'		=>'right',
			'index'		=> 'cool_type',
		));
		// Set Slip Number Column
		$this->addColumn('slip_number', array(
			'header'	=> $helper->__('Slip Number'),
			'align'		=>'right',
			'index'		=> 'slip_number',
		));
		// Set Shipment Date Column
		$this->addColumn('shipment_date', array(
			'header'	=> $helper->__('Shipment Date'),
			'align'		=>'right',
			'type'		=> 'date',
			'format'    => 'Y/MM/dd',
			'index'		=> 'shipment_date',
		));
		// Set Delivery Date Column
		$this->addColumn('delivery_date', array(
			'header'	=> $helper->__('Delivery Date'),
			'align'		=>'right',
			'type'		=> 'date',
			'format'    => 'Y/MM/dd',
			'index'		=> 'delivery_date',
		));
		// Set Delivery Time Column
		$this->addColumn('delivery_time', array(
			'header'	=> $helper->__('Delivery Time'),
			'align'		=>'right',
			'index'		=> 'delivery_time',
		));
		// Set Customer ID Column
		$this->addColumn('customer_id', array(
			'header'	=> $helper->__('Customer ID'),
			'align'		=>'right',
			'index'		=> 'customer_id',
		));
		// Set Customer Telephone Column
		$this->addColumn('customer_tel', array(
			'header'	=> $helper->__('Customer Telephone'),
			'align'		=>'right',
			'index'		=> 'customer_tel',
		));
		// Set Customer Telephone Branch Number Column
		$this->addColumn('customer_tel_branch_num', array(
			'header'	=> $helper->__('Customer Telephone Branch Number'),
			'align'		=>'right',
			'index'		=> 'customer_tel_branch_num',
		));
		// Set Customer Post Code Column
		$this->addColumn('customer_postcode', array(
			'header'	=> $helper->__('Customer Post Code'),
			'align'		=>'right',
			'index'		=> 'customer_postcode',
		));
		// Set Customer Address Column
		$this->addColumn('customer_address', array(
			'header'	=> $helper->__('Customer Address'),
			'align'		=>'right',
			'index'		=> 'customer_address',
		));
		// Set Customer Apartment Name Column
		$this->addColumn('customer_apart_name', array(
			'header'	=> $helper->__('Customer Apartment Name'),
			'align'		=>'right',
			'index'		=> 'customer_apart_name',
		));
		// Set Department 1 of Customer Column
		$this->addColumn('customer_department1', array(
			'header'	=> $helper->__('Department 1 of Customer'),
			'align'		=>'right',
			'index'		=> 'customer_department1',
		));
		// Set Department 2 of Customer Column
		$this->addColumn('customer_department2', array(
			'header'	=> $helper->__('Department 2 of Customer'),
			'align'		=>'right',
			'index'		=> 'customer_department2',
		));
		// Set Customer Full Name Column
		$this->addColumn('customer_full_name', array(
			'header'	=> $helper->__('Customer Full Name'),
			'align'		=>'right',
			'index'		=> 'customer_full_name',
		));
		// Set Customer Full Name (kana) Column
		$this->addColumn('customer_full_name_kana', array(
			'header'	=> $helper->__('Customer Full Name (kana)'),
			'align'		=>'right',
			'index'		=> 'customer_full_name_kana',
		));
		// Set Customer Prefix Column
		$this->addColumn('customer_prefix', array(
			'header'	=> $helper->__('Customer Prefix'),
			'align'		=>'right',
			'index'		=> 'customer_prefix',
		));
		// Set Store Member Number Column
		$this->addColumn('store_member_num', array(
			'header'	=> $helper->__('Store Member Number'),
			'align'		=>'right',
			'index'		=> 'store_member_num',
		));
		// Set Store Telephone Column
		$this->addColumn('store_tel', array(
			'header'	=> $helper->__('Store Telephone'),
			'align'		=>'right',
			'index'		=> 'store_tel',
		));
		// Set Store Telephone Branch Number Column
		$this->addColumn('store_tel_branch_num', array(
			'header'	=> $helper->__('Store Telephone Branch Number'),
			'align'		=>'right',
			'index'		=> 'store_tel_branch_num',
		));
		// Set Store PostCode Column
		$this->addColumn('store_postcode', array(
			'header'	=> $helper->__('Store PostCode'),
			'align'		=>'right',
			'index'		=> 'store_postcode',
		));
		// Set Store Address Column
		$this->addColumn('store_address', array(
			'header'	=> $helper->__('Store Address'),
			'align'		=>'right',
			'index'		=> 'store_address',
		));
		// Set Store Apartment Name Column
		$this->addColumn('store_apart_name', array(
			'header'	=> $helper->__('Store Apartment Name'),
			'align'		=>'right',
			'index'		=> 'store_apart_name',
		));
		// Set Store Name Column
		$this->addColumn('store_name', array(
			'header'	=> $helper->__('Store Name'),
			'align'		=>'right',
			'index'		=> 'store_name',
		));
		// Set Store Name (Kana) Column
		$this->addColumn('store_name_kana', array(
			'header'	=> $helper->__('Store Name (Kana)'),
			'align'		=>'right',
			'index'		=> 'store_name_kana',
		));
		// Set Product ID 1 Column
		$this->addColumn('product_id_1', array(
			'header'	=> $helper->__('Product ID 1'),
			'align'		=>'right',
			'index'		=> 'product_id_1',
		));
		// Set Product Name 1 Column
		$this->addColumn('product_name_1', array(
			'header'	=> $helper->__('Product Name 1'),
			'align'		=>'right',
			'index'		=> 'product_name_1',
		));
		// Set Product ID 2 Column
		$this->addColumn('product_id_2', array(
			'header'	=> $helper->__('Product ID 2'),
			'align'		=>'right',
			'index'		=> 'product_id_2',
		));
		// Set Product Name 2 Column
		$this->addColumn('product_name_2', array(
			'header'	=> $helper->__('Product Name 2'),
			'align'		=>'right',
			'index'		=> 'product_name_2',
		));
		// Set Handling 1 Column
		$this->addColumn('handling_1', array(
			'header'	=> $helper->__('Handling 1'),
			'align'		=>'right',
			'index'		=> 'handling_1',
		));
		// Set Handling 2 Column
		$this->addColumn('handling_2', array(
			'header'	=> $helper->__('Handling 2'),
			'align'		=>'right',
			'index'		=> 'handling_2',
		));
		// Set Comment Column
		$this->addColumn('order_id', array(
			'header'	=> $helper->__('Comment'),
			'align'		=>'right',
			'index'		=> 'order_id',
		));
		// Set Cash on Delivery Amount(including tax) Column
		$this->addColumn('cod_amount', array(
			'header'	=> $helper->__('Cash on Delivery Amount(including tax)'),
			'align'		=>'right',
			'index'		=> 'cod_amount',
		));
		// Set Amount of Tax Column
		$this->addColumn('tax_amount', array(
			'header'	=> $helper->__('Amount of Tax'),
			'align'		=>'right',
			'index'		=> 'tax_amount',
		));
		// Set Held at Yamato Office Column
		$this->addColumn('held_yamato_office', array(
			'header'	=> $helper->__('Held at Yamato Office'),
			'align'		=>'right',
			'index'		=> 'held_yamato_office',
		));
		// Set Yamato Office ID Column
		$this->addColumn('yamato_office_id', array(
			'header'	=> $helper->__('Yamato Office ID'),
			'align'		=>'right',
			'index'		=> 'yamato_office_id',
		));
		// Set Number of Issued Column
		$this->addColumn('number_of_issued', array(
			'header'	=> $helper->__('Number of Issued'),
			'align'		=>'right',
			'index'		=> 'number_of_issued',
		));
		// Set The Number Display Flag Column
		$this->addColumn('number_display_flag', array(
			'header'	=> $helper->__('The Number Display Flag'),
			'align'		=>'right',
			'index'		=> 'number_display_flag',
		));
		// Set Invoice Customer ID Column
		$this->addColumn('invoice_customer_id', array(
			'header'	=> $helper->__('Invoice Customer ID'),
			'align'		=>'right',
			'index'		=> 'invoice_customer_id',
		));
		// Set Invoice Class ID Column
		$this->addColumn('invoice_class_id', array(
			'header'	=> $helper->__('Invoice Class ID'),
			'align'		=>'right',
			'index'		=> 'invoice_class_id',
		));
		// Set Shipping Charge Number Column
		$this->addColumn('shipping_charge_number', array(
			'header'	=> $helper->__('Shipping Charge Number'),
			'align'		=>'right',
			'index'		=> 'shipping_charge_number',
		));
		// Set Card Payment Entry Column
		$this->addColumn('card_pay_entry', array(
			'header'	=> $helper->__('Card Payment Entry'),
			'align'		=>'right',
			'index'		=> 'card_pay_entry',
		));
		// Set Card Payment Shop Number Column
		$this->addColumn('card_pay_shop_number', array(
			'header'	=> $helper->__('Card Payment Shop Number'),
			'align'		=>'right',
			'index'		=> 'card_pay_shop_number',
		));
		// Set Card Payment Acceptance Number 1 Column
		$this->addColumn('card_pay_acceptance_number1', array(
			'header'	=> $helper->__('Card Payment Acceptance Number 1'),
			'align'		=>'right',
			'index'		=> 'card_pay_acceptance_number1',
		));
		// Set Card Payment Acceptance Number 2 Column
		$this->addColumn('card_pay_acceptance_number2', array(
			'header'	=> $helper->__('Card Payment Acceptance Number 2'),
			'align'		=>'right',
			'index'		=> 'card_pay_acceptance_number2',
		));
		// Set Card Payment Acceptance Number 3 Column
		$this->addColumn('card_pay_acceptance_number3', array(
			'header'	=> $helper->__('Card Payment Acceptance Number 3'),
			'align'		=>'right',
			'index'		=> 'card_pay_acceptance_number3',
		));
		// Set Enable Email to Notice Schedule Column
		$this->addColumn('enable_email_notice_schedule', array(
			'header'	=> $helper->__('Enable Email to Notice Schedule'),
			'align'		=>'right',
			'index'		=> 'enable_email_notice_schedule',
		));
		// Set Email to Notice Schedule Column
		$this->addColumn('email_notice_schedule', array(
			'header'	=> $helper->__('Email to Notice Schedule'),
			'align'		=>'right',
			'index'		=> 'email_notice_schedule',
		));
		// Set Input Equipment Column
		$this->addColumn('input_equipment', array(
			'header'	=> $helper->__('Input Equipment'),
			'align'		=>'right',
			'index'		=> 'input_equipment',
		));
		// Set Email Message to Notice Schedule Column
		$this->addColumn('email_message_notice_schedule', array(
			'header'	=> $helper->__('Email Message to Notice Schedule'),
			'align'		=>'right',
			'index'		=> 'email_message_notice_schedule',
		));
		// Set Enable Email to Notice Complete Column
		$this->addColumn('enable_email_notice_complete', array(
			'header'	=> $helper->__('Enable Email to Notice Complete'),
			'align'		=>'right',
			'index'		=> 'enable_email_notice_complete',
		));
		// Set Email to Notice Complete Column
		$this->addColumn('email_notice_complete', array(
			'header'	=> $helper->__('Email to Notice Complete'),
			'align'		=>'right',
			'index'		=> 'email_notice_complete',
		));
		// Set Email Message to Notice Complete Column
		$this->addColumn('email_message_notice_complete', array(
			'header'	=> $helper->__('Email Message to Notice Complete'),
			'align'		=>'right',
			'index'		=> 'email_message_notice_complete',
		));
		// Set Enable to Use Receiving AgentColumn
		$this->addColumn('rec_agent_flag', array(
			'header'	=> $helper->__('Enable to Use Receiving Agent'),
			'align'		=>'right',
			'index'		=> 'rec_agent_flag',
		));
		// Set Receiving Agent QR code Column
		$this->addColumn('rec_agent_qr_code', array(
			'header'	=> $helper->__('Receiving Agent QR code'),
			'align'		=>'right',
			'index'		=> 'rec_agent_qr_code',
		));
		// Set Tracking Column
		$this->addColumn('rec_agent_amount', array(
			'header'	=> $helper->__('Receiving Agent Amount(including tax)'),
			'align'		=>'right',
			'index'		=> 'rec_agent_amount',
		));
		// Set Receiving Agent Amount of Tax Column
		$this->addColumn('rec_agent_amount_of_tax', array(
			'header'	=> $helper->__('Receiving Agent Amount of Tax'),
			'align'		=>'right',
			'index'		=> 'rec_agent_amount_of_tax',
		));
		// Set Receiving Agent Invoice PostCode Column
		$this->addColumn('rec_agent_invoice_postcode', array(
			'header'	=> $helper->__('Receiving Agent Invoice PostCode'),
			'align'		=>'right',
			'index'		=> 'rec_agent_invoice_postcode',
		));
		// Set Receiving Agent Invoice Address Column
		$this->addColumn('rec_agent_invoice_address', array(
			'header'	=> $helper->__('Receiving Agent Invoice Address'),
			'align'		=>'right',
			'index'		=> 'rec_agent_invoice_address',
		));
		// Set Receiving Agent Invoice Apartment Name Column
		$this->addColumn('rec_agent_invoice_appat_name', array(
			'header'	=> $helper->__('Receiving Agent Invoice Apartment Name'),
			'align'		=>'right',
			'index'		=> 'rec_agent_invoice_appat_name',
		));
		// Set Department 1 of Receiving Agent Column
		$this->addColumn('rec_agent_department1', array(
			'header'	=> $helper->__('Department 1 of Receiving Agent'),
			'align'		=>'right',
			'index'		=> 'rec_agent_department1',
		));
		// Set Tracking Column
		$this->addColumn('rec_agent_department2', array(
			'header'	=> $helper->__('Department 2 of Receiving Agent'),
			'align'		=>'right',
			'index'		=> 'rec_agent_department2',
		));
		// Set Receiving Agent Invoice Name Column
		$this->addColumn('rec_agent_invoice_name', array(
			'header'	=> $helper->__('Receiving Agent Invoice Name'),
			'align'		=>'right',
			'index'		=> 'rec_agent_invoice_name',
		));
		// Set Receiving Agent Invoice Name (kana) Column
		$this->addColumn('rec_agent_invoice_name_kana', array(
			'header'	=> $helper->__('Receiving Agent Invoice Name (kana)'),
			'align'		=>'right',
			'index'		=> 'rec_agent_invoice_name_kana',
		));
		// Set Tracking Number Column
		$this->addColumn('rec_agent_ref_name', array(
			'header'	=> $helper->__('Receiving Agent Reference Name'),
			'align'		=>'right',
			'index'		=> 'rec_agent_ref_name',
		));
		// Set Receiving Agent Reference PostCode Column
		$this->addColumn('rec_agent_ref_postcode', array(
			'header'	=> $helper->__('Receiving Agent Reference PostCode'),
			'align'		=>'right',
			'index'		=> 'rec_agent_ref_postcode',
		));
		// Set Receiving Agent Reference Address Column
		$this->addColumn('rec_agent_ref_address', array(
			'header'	=> $helper->__('Receiving Agent Reference Address'),
			'align'		=>'right',
			'index'		=> 'rec_agent_ref_address',
		));
		// Set Receiving Agent Reference Apartment Name Column
		$this->addColumn('rec_agent_ref_apart_name', array(
			'header'	=> $helper->__('Receiving Agent Reference Apartment Name'),
			'align'		=>'right',
			'index'		=> 'rec_agent_ref_apart_name',
		));
		// Set Receiving Agent Reference Telephone Number Column
		$this->addColumn('rec_agent_tel_num', array(
			'header'	=> $helper->__('Receiving Agent Reference Telephone Number'),
			'align'		=>'right',
			'index'		=> 'rec_agent_tel_num',
		));
		// Set Receiving Agent Number Column
		$this->addColumn('rec_agent_number', array(
			'header'	=> $helper->__('Receiving Agent Number'),
			'align'		=>'right',
			'index'		=> 'rec_agent_number',
		));
		// Set Receiving Agent Product Name Column
		$this->addColumn('rec_agent_product_name', array(
			'header'	=> $helper->__('Receiving Agent Product Name'),
			'align'		=>'right',
			'index'		=> 'rec_agent_product_name',
		));
		// Set Receiving Agent Comment Column
		$this->addColumn('rec_agent_comment', array(
			'header'	=> $helper->__('Receiving Agent Comment'),
			'align'		=>'right',
			'index'		=> 'rec_agent_comment',
		));

		// Set Export Types
		$this->addExportType('*/*/exportCsv', $helper->__('CSV'));
		$this->addExportType('*/*/exportXml', $helper->__('XML'));
		
		// Prepare parent Column
		return parent::_prepareColumns();
	}

    /**
     * Prepare grid URL
     *
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('id' => $row->getSlipId()));
    }
}