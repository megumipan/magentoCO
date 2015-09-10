<?php
/*
 * Irvine Systems Award Points
 *
 * NOTICE OF LICENSE
 * This source file is subject to the Revised BSD License which is bundled with
 * this package in the files LICENSE.txt and LICENSE.html.
 *
 * @category	Magento Sale Extension
 * @package		IrvineSystems_AwardPoints
 * @author Irvine Systems, Inc.
 * @copyright Copyright (c) 2015, Irvine Systems, Inc. (http://irvinesystems.co.jp/)
 * @license     Revised BSD License
 */

class IrvineSystems_Awardpoints_Block_Adminhtml_Customertpoints_Grid extends Mage_Adminhtml_Block_Widget_Grid
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
		$this->setId('customertpointsGrid');
		$this->setDefaultSort('customer_id');
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
        // Prepare data Collection
		$collection = Mage::getResourceModel('awardpoints/customer_collection')->addCustomerName();
		
		// If we are coming from statistics Filter the collection with the Customer Id selected in staatistics
		if ($cId = $this->getRequest()->getParam('cId')) $collection->addCustomerFilter($cId);
		
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
		// Set Main Id Column
		$this->addColumn('customer_id', array(
			'header'	=> Mage::helper('awardpoints')->__('ID'),
			'align'		=>'right',
			'width'		=> '50px',
			'index'		=> 'customer_id',
		));

		// Set Customer Full Name Column
		$this->addColumn('customer_name', array(
			'header'	=> Mage::helper('awardpoints')->__('Customer Full Name'),
			'align'		=>'left',
			'index'		=> 'customer_name',
		));

		// Set Client Email Column
		$this->addColumn('email', array(
				'header'	=> Mage::helper('awardpoints')->__('Customer Email'),
				'align'		=>'left',
				'index'		=> 'email',
		));

		// Set Order ID Column
		$this->addColumn('order_id', array(
				'header'	=> Mage::helper('awardpoints')->__('Order ID'),
				'width'		=> '150px',
				'align'		=>'right',
				'index'		=> 'order_id',
		));

		// Set Points Type Column
		$this->addColumn('points_type', array(
				'header'	=> Mage::helper('awardpoints')->__('Type of Points'),
				'index'		=> 'points_type',
				'align'		=>'left',
				'width'		=> '150px',
				'type'		=> 'options',
				'options'	=> Mage::getModel('awardpoints/awardpoints')->getPointsTypes(),
			));

		// Set Accumulated Points Column
		$this->addColumn('points_current', array(
				'header'	=> Mage::helper('awardpoints')->__('Gained Points'),
				'align'		=> 'right',
				'index'		=> 'points_current',
				'width'		=> '50px',
				'filter'	=> false,
		));

		// Set Spent Points Column
		$this->addColumn('points_spent', array(
				'header'	=> Mage::helper('awardpoints')->__('Spent Points'),
				'align'		=> 'right',
				'index'		=> 'points_spent',
				'width'		=> '50px',
				'filter'	=> false,
		));

		// Set Obtainment Date Column
		$this->addColumn('date_start', array(
				'header'	=> Mage::helper('awardpoints')->__('Obtainment Date'),
				'width'		=> '100px',
				'align'		=> 'center',
				'index'		=> 'date_start',
		));

		// Set Expiration Date Column
		$this->addColumn('date_end', array(
				'header'	=> Mage::helper('awardpoints')->__('Expiration Date'),
				'width'		=> '100px',
				'align'		=> 'center',
				'index'		=> 'date_end',
		));

		// Set Export Types
		$this->addExportType('*/*/exportCsv', Mage::helper('awardpoints')->__('CSV'));
		$this->addExportType('*/*/exportXml', Mage::helper('awardpoints')->__('XML'));
		
		return parent::_prepareColumns();
	}
	
    /**
     * Prepare massaction options
     *
     */
	protected function _prepareMassaction()
	{
		// Set the field to use for massaction
		$this->setMassactionIdField('account_id');
		// Set a name for the Mass Field
		$this->getMassactionBlock()->setFormFieldName('account_ids');

		// Set the MassAction Options and proprietaries
		$this->getMassactionBlock()->addItem('delete', array(
			 'label'	=> Mage::helper('awardpoints')->__('Delete&nbsp;&nbsp;'),
			 'url'		=> $this->getUrl('*/*/massDelete', array('_current'=>true)),
			 'confirm'	=> Mage::helper('awardpoints')->__('Are you sure you want to delete all selected points?')
		));

		return $this;
	}

    /**
     * Run Afterload updates on all Collection Items
     *
     */
	protected function _afterLoadCollection()
	{
		$this->getCollection()->walk('afterLoad');
		parent::_afterLoadCollection();
	}

    /**
     * Prepare grid URL
     *
     * @return string
     */
    public function getRowUrl($row)
    {
        // Keep the current post data and add the row id
		return $this->getUrl('*/*/edit', array('id' => $row->getId(),'_current'=>true));
    }

}