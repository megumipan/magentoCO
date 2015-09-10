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

class IrvineSystems_JapanPost_Block_Adminhtml_Slips_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
   /**
    * Block constructor, prepare form Container params
    *
    */
    public function __construct()
    {
		// Construct Parent Container
        parent::__construct();

        // Set Block Data
		$this->_objectId = 'id';
        $this->_blockGroup = 'japanpost';
        $this->_controller = 'adminhtml_slips';

		// Remove Delete button and update Save button
		$this->_removeButton('delete');
        $this->_updateButton('save', 'label', Mage::helper('japanpost')->__('Save Slip'));

    }

   /**
    * Prepare Block Header
    *
    * @return string
    */
    public function getHeaderText()
    {
        // Check if we are editing a Rule or create a new one
		$slip = Mage::registry('slip_data');
        return Mage::helper('japanpost')->__("Edit Shipping Slip for Order '%s'", $this->htmlEscape($slip->getOrderId()));
    }
}