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

class IrvineSystems_Sagawa_Adminhtml_MailsController extends IrvineSystems_Sagawa_Controller_Abstract
{
	// Slips mode target
	protected $_target ='mails';

    /**
     * Slips area entry point
     * Render Layout
     * 
     */
	public function indexAction()
	{
		// Initialize Action Sets
		$this->_initAction()
			->_addContent($this->getLayout()->createBlock('sagawa/adminhtml_mails'))
			->renderLayout();
	}

    /**
     * Process the Shipment for all selected Orders
     * 
     */
	public function doShipmentAction() {
		// Get all selected Id to be deleted
		$ruleIds = $this->getRequest()->getParam('slip_ids');
		// Define Success and Errors counters
		$success	= 0;
		$error		= array();
		// Process all data
		foreach ($ruleIds as $ruleId) {
			// Get current Order Id
			$orderId = Mage::getModel('sagawa/slips')->load($ruleId)->getOrderId();
			// Process the shipment, Cath any error into the Error object to return
			try {
				// Validate order shipment
				$shipment = $this->_initShipment($orderId);
				// If we have a shipment process the tracking and notification
				if ($shipment){
					// Save all Shipment information
					$this->_saveShipment($shipment);
					// Update success Counter
					$success++;
				}
				// Catch the Exceptions and add it to the returning Errors array
			}catch (Exception $e){
				// Add the Exception message to the array
				$error[] = $e->getMessage();
			}
		}
		// Set the returning array
		$processResult = array(
				'success_count'	=>	$success,
				'error'			=>	$error
		);
		
		// Return to the Import Page
		$this->_redirect('*/*/');
	}
}