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

class IrvineSystems_Awardpoints_Block_Adminhtml_Stats extends Mage_Adminhtml_Block_Widget_Grid_Container
{
   /**
    * Block constructor, prepare grid params
    *
    */
	public function __construct()
	{
		// Set Block Controller
		$this->_controller = 'adminhtml_stats';
		// Set BlockGroup
		$this->_blockGroup = 'awardpoints';
		// Set Header Title
		$this->_headerText = Mage::helper('awardpoints')->__('Statistics');
		// Construct Parent Container
		parent::__construct();
		// Remove the Add button since it will not be used
		$this->_removeButton('add');
		// Add Check Points button after construction, since we want it after the Add points Button
		$this->_addButton ('check_orders_points', array(
			'label'		=> Mage::helper('awardpoints')->__('Refresh Orders Points'),
			'class'		=> 'save',
			'onclick'	=> 'setLocation(\''.$this->getCheckPointsUrl().'\')'));
	}

    /**
     * Prepare Check Points URL
     *
     * @return string
     */
	public function getCheckPointsUrl()
	{
		return $this->getUrl('*/*/check');
	}
}