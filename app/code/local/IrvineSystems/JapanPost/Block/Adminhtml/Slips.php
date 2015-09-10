<?php
/*
 * Irvine Systems Shipping Japan Jp
 *
 * NOTICE OF LICENSE
 * This source file is subject to the Revised BSD License which is bundled with
 * this package in the files LICENSE.txt and LICENSE.html.
 *
 * @category	Magento Shipping Extension
 * @package		IrvineSystems_JapanPost
 * @author Irvine Systems, Inc.
 * @copyright Copyright (c) 2015, Irvine Systems, Inc. (http://irvinesystems.co.jp/)
 * @license     Revised BSD License
 */

class IrvineSystems_JapanPost_Block_Adminhtml_Slips extends Mage_Adminhtml_Block_Widget_Grid_Container
{
   /**
    * Block constructor, prepare grid params
    *
    */
	public function __construct()
	{
		// Set Block Controller
		$this->_controller = 'adminhtml_slips';
		// Set BlockGroup
		$this->_blockGroup = 'japanpost';
		// Set Header Title
		$this->_headerText = Mage::helper('japanpost')->__('JAPAN POST - Edit and Export Shipping Slips');
		// Construct Parent Container
		parent::__construct();
		// Remove the Add button since it will not be used
		$this->_removeButton('add');
		// Set confirmation message for Clean Action
		$confirmMessage = Mage::helper('japanpost')->__('Are you sure you want to delete all Completed, Canceled and Closed Orders from the Slips Database?');
		// Add a Clean Slips Points button
		$this->_addButton ('clean_slips', array(
			'label'		=> Mage::helper('japanpost')->__('Clean Order Slips'),
			'title'		=> Mage::helper('japanpost')->__("Use Clean Slip for remove all 'Completed' 'Canceled' and 'Closed' Order from the Slip database"),
			'class'		=> 'delete',
			'onclick'   => 'deleteConfirm(\''.$confirmMessage.'\', \'' . $this->getCleanUrl() . '\')'));
	}

    /**
     * Prepare Clean Slips URL
     *
     * @return string
     */
	public function getCleanUrl()
	{
		return $this->getUrl('*/*/clean');
	}
}