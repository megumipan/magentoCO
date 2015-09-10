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
 
class IrvineSystems_Awardpoints_Block_Adminhtml_Referrals_Grid extends Mage_Adminhtml_Block_Widget_Grid
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
		$this->setId('referralsGrid');
		$this->setDefaultSort('referral_id');
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
		$collection = Mage::getResourceModel('awardpoints/referral_collection')->addCustomerName();

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
		$this->addColumn('id', array(
			'header'	=> Mage::helper('awardpoints')->__('ID'),
			'align'		=>'right',
			'width'		=> '50px',
			'index'		=> 'referral_id',
		));

		// Set Customer Full Name Column
		$this->addColumn('customer_name', array(
			'header'	=> Mage::helper('awardpoints')->__('Customer Full Name'),
			'width'		=> '300px',
			'align'		=>'left',
			'index'		=> 'customer_name',
		));

		// Set Customer Email Column
		$this->addColumn('email', array(
			'header'	=> Mage::helper('awardpoints')->__('Customer Email'),
			'width'		=> '250px',
			'align'		=>'left',
			'index'		=> 'email',
		));

		// Set Referred Full Name Column
		$this->addColumn('child_name', array(
			'header'	=> Mage::helper('awardpoints')->__('Referred Full Name'),
			'width'		=> '300px',
			'align'		=>'left',				
			'index'		=> 'child_name',
		));

		// Set Referred email Column
		$this->addColumn('child_email', array(
			'header'	=> Mage::helper('awardpoints')->__('Referred Email'),
			'width'		=> '250px',
			'align'		=>'left',
			'index'		=> 'child_email',
		));

		// Set First Order Status Column
		$this->addColumn('referral_status', array(
			'header'	=> Mage::helper('awardpoints')->__('First Order Status'),
			'align'		=>'left',				
			'index'		=> 'referral_status',
			'width'		=> '150px',
			'type'		=> 'options',
			'options'	=> Mage::getModel('awardpoints/awardpoints')->getReferralStatusTypes(),
		));

		return parent::_prepareColumns();
	}

    /**
     * Prepare massaction options
     *
     */
	protected function _prepareMassaction()
	{
		// Set the field to use for massaction
		$this->setMassactionIdField('referral_id');
		// Set a name for the Mass Field
		$this->getMassactionBlock()->setFormFieldName('referral_ids');

		// Set the MassAction Options and proprietaries
		$this->getMassactionBlock()->addItem('delete', array(
			 'label'	=> Mage::helper('awardpoints')->__('Delete&nbsp;&nbsp;'),
			 'url'		=> $this->getUrl('*/*/massDelete'),
			 'confirm'	=> Mage::helper('awardpoints')->__('Are you sure you want to delete all selected referrals?')
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
}