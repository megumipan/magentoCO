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

class IrvineSystems_Awardpoints_Block_Adminhtml_Stats_Grid extends Mage_Adminhtml_Block_Widget_Grid
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
		$this->setId('statsGrid');
		$this->setDefaultSort('entity_id');
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
		$collection = Mage::getResourceModel('awardpoints/stats_collection')->addNameToSelect();
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
		$this->addColumn('entity_id', array(
			'header'	=> Mage::helper('awardpoints')->__('ID'),
			'align'		=>'right',
			'width'		=> '50px',
			'index'		=> 'entity_id',
		));

		// Set Customer Full Name Column
		$this->addColumn('name', array(
			'header'	=> Mage::helper('awardpoints')->__('Customer Full Name'),
			'align'		=> 'left',
			'index'		=> 'name',
		));

		// Set Customer Email Column
		$this->addColumn('email', array(
			'header'	=> Mage::helper('awardpoints')->__('Customer Email'),
			'align'		=> 'left',
			'index'		=> 'email',
		));
		
		// Set Total Current Points Column
		$this->addColumn('current_points', array(
			'header'	=> Mage::helper('awardpoints')->__('Total Available Points'),
			'align'		=> 'right',
			'width'		=> '150px',
			'index'		=> 'current_points',
			'filter'	=> false,
		));

		// Set Total Accumulated Points Column
		$this->addColumn('tot_points', array(
			'header'	=> Mage::helper('awardpoints')->__('Total Accumulated Points'),
			'align'		=> 'right',
			'width'		=> '150px',
			'index'		=> 'tot_points',
			'filter'	=> false,
		));

		// Set Total Spent Points Column
		$this->addColumn('spent_points', array(
			'header'	=> Mage::helper('awardpoints')->__('Total Spent Points'),
			'align'		=> 'right',
			'width'		=> '150px',
			'index'		=> 'spent_points',
			'filter'	=> false,
		));

		// Set Export Types
		$this->addExportType('*/*/exportCsv', Mage::helper('awardpoints')->__('CSV'));
		$this->addExportType('*/*/exportXml', Mage::helper('awardpoints')->__('XML'));
		
		return parent::_prepareColumns();

	}

    /**
     * Prepare grid URL
     *
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('*/adminhtml_customertpoints/index', array('cId' => $row->getEntityId()));
    }
}