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

class IrvineSystems_Sagawa_Block_Adminhtml_Parcels extends Mage_Adminhtml_Block_Widget_Grid_Container
{
   /**
    * Block constructor, prepare grid params
    *
    */
	public function __construct()
	{
		// Set Block Controller
		$this->_controller = 'adminhtml_parcels';
		// Set BlockGroup
		$this->_blockGroup = 'sagawa';
		// Set Header Title
		$this->_headerText = Mage::helper('sagawa')->__('SAGAWA - Parcels Slips Management');
		// Construct Parent Container
		parent::__construct();
		// Remove the Add button since it will not be used
		$this->_removeButton('add');
		// Set confirmation message for Clean Action
		$confirmMessage = Mage::helper('sagawa')->__('Are you sure you want to delete all Completed, Canceled and Closed Orders from the Slips Database?');
		// Add a Clean Slips Points button
		$this->_addButton ('clean_slips', array(
			'label'		=> Mage::helper('sagawa')->__('Clean Order Slips'),
			'title'		=> Mage::helper('sagawa')->__("Use Clean Slip for remove all 'Completed' 'Canceled' and 'Closed' Order from the Slip database"),
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