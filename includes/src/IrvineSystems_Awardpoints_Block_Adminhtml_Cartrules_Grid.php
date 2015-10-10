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

class IrvineSystems_Awardpoints_Block_Adminhtml_Cartrules_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
   /**
    * Block constructor, prepare Grid params
    *
    */
    public function __construct()
    {
		// Construct Parent Grid
        parent::__construct();

        // Construct Grid Proprieties
        $this->setId('rule_id');
        $this->setDefaultSort('name');
        $this->setDefaultDir('ASC');
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
        $collection = Mage::getModel('awardpoints/cartrules')->getResourceCollection();
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
		$model = Mage::getModel('awardpoints/awardpoints');
        
		// Set Rule Id Column
        $this->addColumn('rule_id', array(
            'header'    => Mage::helper('awardpoints')->__('Rule ID'),
            'align'     =>'right',
            'width'     => '100px',
            'index'     => 'rule_id',
        ));

		// Set Title Column
        $this->addColumn('title', array(
            'header'    => Mage::helper('awardpoints')->__('Rule Name'),
            'align'     =>'left',
            'index'     => 'title',
        ));

		// Set Action Type Column
        $this->addColumn('action_type', array(
            'header'    => Mage::helper('awardpoints')->__('Action Type'),
            'align'     =>'left',
			'width'     => '200px',
            'index'     => 'action_type',
            'type'      => 'options',
            'options'   => $model->getRulesActions(),
        ));

		// Set Status Column
        $this->addColumn('status', array(
            'header'    => Mage::helper('awardpoints')->__('Rule Status'),
            'align'     =>'left',
			'width'     => '100px',
            'index'     => 'status',
			'type'      => 'options',
            'options'   => $model->getRulesStates(),
        ));

        return parent::_prepareColumns();
    }

    /**
     * Prepare grid URL
     *
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('id' => $row->getRuleId()));
    }
}