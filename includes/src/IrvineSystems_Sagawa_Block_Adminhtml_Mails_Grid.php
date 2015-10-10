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

class IrvineSystems_Sagawa_Block_Adminhtml_Mails_Grid extends Mage_Adminhtml_Block_Widget_Grid
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
		$this->setDefaultSort('slip_id');
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
		$collection = Mage::getResourceModel('sagawa/slips_collection')->addOrderStatusFilter($status)->addMailFilter();
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
		$model = Mage::getModel('sagawa/slips');
        // Set Helper Model
        $helper = Mage::helper('sagawa');

		// Set Order Number Column
		$this->addColumn('order_id', array(
			'header'	=> $helper->__('Order Number'),
			'align'		=>'right',
			'index'		=> 'order_id',
		));

		// Set Address Book Code Column
		//$this->addColumn('customer_address_code', array(
		//	'header'	=> $helper->__('Address Book Code'),
		//	'align'		=>'left',
		//	'index'		=> 'customer_address_code',
		//));

		// Set Customer Telephone Column
		$this->addColumn('customer_tel', array(
			'header'	=> $helper->__('Customer Telephone'),
			'align'		=> 'left',
			'index'		=> 'customer_tel',
		));

		// Set Customer Post Code Column
		$this->addColumn('customer_postcode', array(
			'header'	=> $helper->__('Customer Post Code'),
			'align'		=>'left',
			'index'		=> 'customer_postcode',
		));

		// Set Customer Address (1) Column
		$this->addColumn('customer_address_1', array(
			'header'	=> $helper->__('Customer Address (1)'),
			'align'		=> 'left',
			'index'		=> 'customer_address_1',
		));

		// Set Customer Address (2) Column
		$this->addColumn('customer_address_2', array(
			'header'	=> $helper->__('Customer Address (2)'),
			'align'		=> 'left',
			'index'		=> 'customer_address_2',
		));

		// Set Customer Address (3) Column
		$this->addColumn('customer_address_3', array(
			'header'	=> $helper->__('Customer Address (3)'),
			'align'		=> 'left',
			'index'		=> 'customer_address_3',
		));

		// Set Customer Full Name Column
		$this->addColumn('customer_name', array(
			'header'	=> $helper->__('Customer Full Name'),
			'align'		=> 'left',
			'index'		=> 'customer_name',
		));

		// Set Customer Full Name (kana) Column
		$this->addColumn('customer_namekana', array(
			'header'	=> $helper->__('Customer Full Name (kana)'),
			'align'		=> 'left',
			'index'		=> 'customer_namekana',
		));

		// Set Customer Member ID Column
		//$this->addColumn('customer_memberid', array(
		//	'header'	=> $helper->__('Customer Member ID'),
		//	'align'		=> 'left',
		//	'index'		=> 'customer_memberid',
		//));

		// Set Customer ID Column
		$this->addColumn('customer_id', array(
			'header'	=> $helper->__('Customer ID'),
			'align'		=> 'left',
			'index'		=> 'customer_id',
		));
        
		// Set Export Types
		$this->addExportType('*/*/exportCsv', $helper->__('CSV'));
		$this->addExportType('*/*/exportXml', $helper->__('XML'));
		
		// Prepare parent Column
		return parent::_prepareColumns();
	}

    /**
     * Prepare massaction options
     *
     */
	protected function _prepareMassaction()
	{
		// Set the field to use for massaction
		$this->setMassactionIdField('slip_id');
		// Set a name for the Mass Field
		$this->getMassactionBlock()->setFormFieldName('slip_ids');

		// Set the MassAction Options and proprietaries
		$this->getMassactionBlock()->addItem('Shipment', array(
			 'label'	=> Mage::helper('sagawa')->__('Create Shipmentt&nbsp;&nbsp;'),
			 'url'		=> $this->getUrl('*/*/doShipment', array('_current'=>true)),
			 'confirm'	=> Mage::helper('sagawa')->__('Are you sure you want to create the shipment for all selected slips?')
		));

		return $this;
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