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

class IrvineSystems_Yamato_Block_Adminhtml_Slips_Grid extends Mage_Adminhtml_Block_Widget_Grid
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

		// Set Delivery Mode Column
		$this->addColumn('delivary_mode', array(
				'header'	=> $helper->__('Delivery Mode'),
				'align'		=>'right',
				'index'		=> 'delivary_mode',
		));
		// Set Customer Telephone Column
		$this->addColumn('customer_tel', array(
				'header'	=> $helper->__('Customer Telephone'),
				'align'		=>'right',
				'index'		=> 'customer_tel',
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
		// Set Customer Full Name Column
		$this->addColumn('customer_full_name', array(
				'header'	=> $helper->__('Customer Full Name'),
				'align'		=>'right',
				'index'		=> 'customer_full_name',
		));
		// Set Store Telephone Column
		$this->addColumn('store_tel', array(
				'header'	=> $helper->__('Store Telephone'),
				'align'		=>'right',
				'index'		=> 'store_tel',
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
		// Set Store Name Column
		$this->addColumn('store_name', array(
				'header'	=> $helper->__('Store Name'),
				'align'		=>'right',
				'index'		=> 'store_name',
		));
		// Set Product Name 1 Column
		$this->addColumn('product_name_1', array(
				'header'	=> $helper->__('Product Name 1'),
				'align'		=>'right',
				'index'		=> 'product_name_1',
		));
		// Set Product Name 1 Column
		$this->addColumn('comment', array(
				'header'	=> $helper->__('Delivery Comment'),
				'align'		=>'right',
				'index'		=> 'comment',
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