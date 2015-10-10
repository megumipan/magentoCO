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

class IrvineSystems_Sagawa_Controller_Abstract extends Mage_Adminhtml_Controller_Action
{
	// Module Code definition
	protected $_code = 'sagawa';
	
    /**
     * Controller Initialization
     * 
     * @return IrvineSystems_Sagawa_Adminhtml_Abstract
     */
	protected function _initAction()
	{
		//load the Layout
		$this->loadLayout()
			->_setActiveMenu('sales')
			->_addBreadcrumb(Mage::helper('sagawa')->__('Shipping Slips'), Mage::helper('sagawa')->__('Shipping Slips'));

		return $this;
	}

	/** ====================================================================================== **/
	/** ============================ EDIT EXPORT ABSTRACT METHODS ============================ **/
	/** ====================================================================================== **/

    /**
     * Clean Slips Database
     * 
     */
	public function cleanAction()
	{
		// Order statuses which can be deleted
		$status = "complete','canceled','closed";
		// Get Slips Model
		$model = Mage::getModel('sagawa/slips');
		// Get Slips Model
		$slips = $model->getCollection();

		// Count the total amount of order in the slip database
		$numOfSlips = count($slips);

		// Get order to delete collection
		$slips = $model->getCollection()->addOrderStatusFilter($status);

		// Set orders and Updates Counter
		$numOfUpdates=0;

		// Delete each Listing in the selection
		foreach ($slips as $slip) {
			$slipData = $model->load($slip->getSlipId());
			$slipData->delete();
			$numOfUpdates++;
		}

		// Set the Returning message for the Admin according to the Results
		if($numOfUpdates > 0){
			Mage::getSingleton('adminhtml/session')
				->addSuccess(Mage::helper('sagawa')
				->__('Full check was proceed on %d slips.<br/>A total of %d slips data were removed from the database.', $numOfSlips, $numOfUpdates)
			);
		}else if($numOfSlips > 0){
			Mage::getSingleton('adminhtml/session')
				->addSuccess(Mage::helper('sagawa')
				->__('Full check was proceed on %d slips.<br/>No deletable slips were found.', $numOfSlips)
			);
		}else{
			Mage::getSingleton('adminhtml/session')
				->addSuccess(Mage::helper('sagawa')
				->__('There are no slips data to be checked')
			);
		};

		// Return to the previous page
		$this->_redirect('*/*/');
	}

    /**
     * Edit Listing Event Handler
     * The function will handle the Edit listing event
     * 
     */
    public function editAction()
    {
		// Load model data for the selected listing
        $id = $this->getRequest()->getParam('id');
		$model = Mage::getModel('sagawa/slips');

		// Errror control if a slip get erased after beign selected
		if ($id) {
            $model->load($id);
            if (! $model->getSlipId()) {
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('sagawa')->__('This slip no longer exists'));
                $this->_redirect('*/*');
                return;
            }
        }

		// Set the Form Data
        $data = Mage::getSingleton('adminhtml/session')->getPageData(true);
		if($data) $model->addData($data);

		// Update Shipping Slips Register
        Mage::register('slip_data', $model);

		// Load Layout and set Block Parameters
        $this->loadLayout();
        $this->_setActiveMenu('sales');
        $block = $this->getLayout()->createBlock('sagawa/adminhtml_'.$this->_target.'_edit')
            ->setData('action', $this->getUrl('*/sagawa_'.$this->_target.'/save'));

		// Enable Javasript for the Page
        $this->getLayout()->getBlock('head')
            ->setCanLoadExtJs(true)
            ->setCanLoadRulesJs(true);

		// Add main and Tabs content to layout
		$this->_addContent($block)
			->_addLeft($this->getLayout()->createBlock('sagawa/adminhtml_'.$this->_target.'_edit_tabs'))
            ->renderLayout();
    }

    /**
     * Save Listing Data
     * The function will Update the Data to be saved
     * 
     */
	public function saveAction()
	{
		// Check if we have POST information to process
		// This may never happen, but it is better to prevent it
		if (!$this->getRequest()->getParams()) {
				Mage::getSingleton('adminhtml/session')->addError(Mage::helper('sagawa')->__('Slip data to be saved were not found. Please refresh the page and try again'));
				$this->_redirect('*/*');
				return;
		}

		// Set the data Array
		$data = $this->getRequest()->getParams();
		// Get Slips Model
		$model = Mage::getModel('sagawa/slips');

		//Reference to slip Id
		$slipId = null;
		if (isset($data['slip_id'])) $slipId = $data['slip_id'];
		
		// If the Slip id is missing stopr processing and go back to Grid
		if (empty($slipId)){
			Mage::throwException(Mage::helper('sagawa')->__('No slips were specified. Please check the Slip Id'));
               $this->_redirect('*/*/');
			return;
		}
		
		// Load the current Slip into the model
		$model->load($slipId);
		// Check if by any change the ids are not matching
		if ($slipId != $model->getSlipId()) {
			Mage::throwException(Mage::helper('sagawa')->__('Wrong slip specified.'));
               $this->_redirect('*/*/');
			return;
		}

		// Validate alkl data
		$validateResult = $model->validateData(new Varien_Object($data));

		// If the data is invalid parse the error message and return to edit
		if (!$validateResult) {
			foreach($validateResult as $errorMessage) {
				$this->_getSession()->addError($errorMessage);
			}
			$this->_getSession()->setPageData($data);
			$this->_redirect('*/*/edit', array('id'=>$model->getId()));
			return;
		}

		// Set the Model Data
		$model->loadPost($data);
		// Set singleton page data
		Mage::getSingleton('adminhtml/session')->setPageData($model->getData());
		// Save the Model
		$model->save();

		// Inform the admin Panel for the success
		Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('sagawa')->__('Slip was successfully saved'));
		// Reset Singleton Page Data
		Mage::getSingleton('adminhtml/session')->setPageData(false);

		// Return to main Grid
		$this->_redirect('*/*/');
	}

    /**
     * Export to CSV File Event Handler
     * The function will export all data in the collection in a CSV File
     * 
     */
	public function exportCsvAction()
	{
		$fileName = 'Sagawa_'.ucfirst($this->_target).'_Slips.csv';
		// render the content in CSV Format
		$content = $this->getLayout()->createBlock('sagawa/adminhtml_'.$this->_target.'_pgrid')->getCsv();
		// Convert the content format into SJIS-win encoding
        $content = mb_convert_encoding($content, 'SJIS-win', 'auto');
		// Start the Download
        $this->_sendUploadResponse($fileName, $content);
    }

    /**
     * Export to XML File Event Handler
     * The function will export all data in the collection in a CSV File
     * 
     */
    public function exportXmlAction()
    {
		$fileName = 'Sagawa_'.ucfirst($this->_target).'_Slips.csv';
		// render the content in CSV Format
		$content = $this->getLayout()->createBlock('sagawa/adminhtml_'.$this->_target.'_pgrid')->getXml();
		// Convert the content format into SJIS-win encoding
        $content = mb_convert_encoding($content, 'SJIS-win', 'auto');
		// Start the Download
        $this->_sendUploadResponse($fileName, $content);
    }

    /**
     * Prepare file download response
     *
     * @todo remove in 1.3
     * @deprecated please use $this->_prepareDownloadResponse()
     * @see Mage_Adminhtml_Controller_Action::_prepareDownloadResponse()
     * @param string $fileName
     * @param string $content
     * @param string $contentType
     */
    protected function _sendUploadResponse($fileName, $content, $contentType='application/octet-stream')
    {
        $this->_prepareDownloadResponse($fileName, $content, $contentType);
    }


	/** ====================================================================================== **/
	/** ========================= SHIPMENT CREATION ABSTRACT METHODS ========================= **/
	/** ====================================================================================== **/

    /**
     * Inizialize and validate shipment
     * 
     * @param int $IncrementId Unique order id
     * 
     * @return Mage_Sales_Model_Order_Shipment
     * 
     * @throws Exception
     */
    protected function _initShipment($IncrementId)
    {
    	// Get Order data
        $order = Mage::getModel('sales/order')->loadByIncrementId($IncrementId);
        
		// Check if the order is available
        if(!$order->getId()) throw new Exception(Mage::helper($this->_code)->__('The order Number: %s, was not found on the order database', $IncrementId));

        // load shipment collection
		$shipmentCollection = $order->getShipmentsCollection();
		
		// Check if the Order already have a shipment
        $shipmentId = false;
		if($shipmentCollection->count()>0){
            // If we have a shipment load it and return it
			$shipmentData = $shipmentCollection->getData();
            $shipmentId   = $shipmentData[0]['entity_id'];
            $shipment = Mage::getModel('sales/order_shipment')->load($shipmentId);
	        return $shipment;
        }

        // if the order do not have a shipment yet, we need to create it
		$shipment = false;
		// Check if the shipment can be created for the order
		if ($order->getForcedDoShipmentWithInvoice()) {
			// TODO: automatically create invoices in this case?
			throw new Exception(Mage::helper($this->_code)->__('The order Number: %s, cannot be shipped, please create the invoice before import', $IncrementId));
		}
		if (!$order->canShip()) {
			throw new Exception(Mage::helper($this->_code)->__('The order Number: %s, cannot be shipped', $IncrementId));
		}

		// Check and get all products quantities in the order to be shipped
		$prodQtys = $this->_getItemQtys($order);
        // Prepare the shipment
		$shipment = Mage::getModel('sales/service_order', $order)->prepareShipment($prodQtys);
		// Register the shipment
		$shipment->register();
		// Return the shipment
        return $shipment;
    }

    /**
     * Process All Order Quantities to be shipped
     * 
     * @param Mage_Sales_Model_Order Order Object
     * 
     * @return array Quantities to be shipped
     */
    protected function _getItemQtys($order){
		// Process each items in the order
        foreach ($order->getAllItems() as $item) {
            // Get all available quantities for the Item
			$canceled    = (float)$item->getQtyCanceled();
            $ordered     = (float)$item->getQtyOrdered();
            $refunded    = (float)$item->getQtyRefunded();
            $shipped     = (float)$item->getQtyShipped();

			// Calculate the actually ordered quantity
            $toBeShip = $ordered-$canceled-$refunded-$shipped;
			// Add the quantity for the item to the returning array
            $result[$item->getId()] =  $toBeShip;
        }
		// Return the resulted array
        return $result;
    }

	/**
	 * Add shipment tracking information to the given shipment
	 * @param Mage_Sales_Model_Order_Shipment $shipment
	 *
	 * @return IrvineSystems_Sagawa_Adminhtml_ImportController
	 */
	protected function _saveShipment($shipment)
	{
		// Set Order Process Status
		$shipment->getOrder()->setIsInProcess(true);
		// update the order transaction and save it
		$transactionSave = Mage::getModel('core/resource_transaction')
		->addObject($shipment)
		->addObject($shipment->getOrder())
		->save();
		// return
		return $this;
	}
}