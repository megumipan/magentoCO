<?php
/*
 * Irvine Systems Shipping Japan Ymt
 *
 * NOTICE OF LICENSE
 * This source file is subject to the Revised BSD License which is bundled with
 * this package in the files LICENSE.txt and LICENSE.html.
 *
 * @category	Magento Shipping Extension
 * @package		IrvineSystems_Yamato
 * @author Irvine Systems, Inc.
 * @copyright Copyright (c) 2015, Irvine Systems, Inc. (http://irvinesystems.co.jp/)
 * @license     Revised BSD License
 */

class IrvineSystems_Yamato_Adminhtml_ImportController extends Mage_Adminhtml_Controller_Action
{
	// CSV columns map
	protected $_arraymap = array(
	'customer_number','delivary_mode','cool_type',
	'tracking_number', // Renamed 'slip_number' into 'tracking_number' during import
	'shipment_date','delivery_date','delivery_time','customer_id','customer_tel','customer_tel_branch_num','customer_postcode','customer_address','customer_apart_name',
	'customer_department1','customer_department2','customer_full_name','customer_full_name_kana','customer_prefix','store_member_num','store_tel','store_tel_branch_num',
	'store_postcode','store_address','store_apart_name','store_name','store_name_kana','product_id_1','product_name_1','product_id_2','product_name_2','handling_1',
	'handling_2','order_id', // The Field 'comment' Used for save Order Id
	'cod_amount','tax_amount','held_yamato_office','yamato_office_id','number_of_issued','number_display_flag','invoice_customer_id','invoice_class_id','shipping_charge_number',
	'card_pay_entry','card_pay_shop_number','card_pay_acceptance_number1','card_pay_acceptance_number2','card_pay_acceptance_number3','enable_email_notice_schedule',
	'email_notice_schedule','input_equipment','email_message_notice_schedule','enable_email_notice_complete','email_notice_complete','email_message_notice_complete',
	'rec_agent_flag','rec_agent_qr_code','rec_agent_amount','rec_agent_amount_of_tax','rec_agent_invoice_postcode','rec_agent_invoice_address',
	'rec_agent_invoice_appat_name','rec_agent_department1','rec_agent_department2','rec_agent_invoice_name','rec_agent_invoice_name_kana','rec_agent_ref_name',
	'rec_agent_ref_postcode','rec_agent_ref_address','rec_agent_ref_apart_name','rec_agent_tel_num','rec_agent_number','rec_agent_product_name','rec_agent_comment');

	// Module Code definition
	protected $_code = 'yamato';
	
	/**
     * Import area entry point
     * Render Layout
     * 
     */
    public function indexAction(){
		// Initialize Layout
        $this->loadLayout();
        $this->_setActiveMenu('sales');
        $this->_addContent($this->getLayout()->createBlock($this->_code .'/adminhtml_import_edit'));
        $this->renderLayout();
    }

    /**
     * Validate uploaded files action.
     * @return void
     */
    public function validateAction()
    {
		// Get Post Data
        $data = $this->getRequest()->getPost();
		
		// Proceed only if data are available
        if ($data) {
            try{
                // Get email bool Value
				$is_email = isset($data['mail_bool']) ? true : false;

                // Disable Layout
				$this->loadLayout(false);
                // Import model Initialization
                $importModel = Mage::getModel($this->_code .'/import');
                $importModel->setData($data)->setUploadFile();

                // Get all data from the uploaded file
                $fileData = $this->_convert( $importModel->getUploadFile() );

				// Update all shipment information for the imported orders
				$res = $this->_createShipments($fileData, $is_email);

				// Set render information about the record processed
                if( count( $res['error'] ) > 0 )
                    $this->_getSession()->addError(
                        $this->__('Tracking upload').' '.
                        $this->__('error count') .' : '. count($res['error'])
                        );
                $this->_getSession()->addSuccess(
                        $this->__('Tracking upload').' '.
                        $this->__('Success count') .' : '. $res['success_count']
                    );
			// Catch error exception
            }catch (Exception $e){
                $this->_getSession()->addError($e->getMessage());
            }

			// Return to the Import Page
			$this->_redirect('*/*/');

		// Handle file upload errors
        } elseif ($this->getRequest()->isPost() && empty($_FILES)) {
            $this->loadLayout(false);
            $resultBlock = $this->getLayout()->getBlock('import.frame.result');
            $resultBlock->addError(Mage::helper($this->_code)->__('File was not uploaded'));
            $this->_redirect('*/*/index');
        } else {
            $this->_getSession()->addError(Mage::helper($this->_code)->__('Data is invalid or file is not uploaded'));
            $this->_redirect('*/*/index');
        }
    }

    /**
     * Convert CSV file into mapped data array
     * @param string $file file name and path location
     * @return array
     */
    protected function _convert($file ) {
		// Define returning array
		$ret = array();
        // read all data and convert it into a string
        $strData = mb_convert_encoding(file_get_contents($file), 'UTF-8', 'auto');
		// Normalize format to LF
		$strData = str_replace(array("\r\n", "\r"), "\n", $strData); 
		// Break each line row into array		
        $lines = str_getcsv( $strData, "\n\"");
		// Process each line of data into array
		foreach ($lines as $key=>$line) {
            // Skip the label row
			if ($key == 0) continue;
            // Skip the empty rows
            if (empty($line)) continue;
			// Get a rowdata array to be mapped
			$rowData = str_getcsv($line);
			// process each data
			for ($i = 0; $i < count($this->_arraymap); $i++) {
				// Map all data
				$lineData[$this->_arraymap[$i]] = $rowData[$i];
			}
			//add the mapped line to the return array
			$csvData[] = $lineData;
        }
		// Return the resoult
        return $csvData;
    }

    /**
     * Crate all Shipments for the processing orders
     * @param array $fileData
     * @param bool $is_email
     * 
     * @return array Success and Errors results
     */
    protected function _createShipments($fileData, $is_email = false)
    {
        // Define Success and Errors counters
		$success	= 0;
        $error		= array();
		// Process all data
        for($i=0; $i < count($fileData); $i++ )
        {
            // Set rawData
			$rowData = $fileData[$i];

            // Skip invalid data
			if(empty($rowData['order_id'])||empty($rowData['tracking_number']))continue;

            // Process the shipment, Cath any error into the Error object to return
			try {
				// Remove any blank space from the ids
				$IncrementId = trim($rowData['order_id']);
                $trackNumber = trim($rowData['tracking_number']);
				
				// Validate order shipment
                $shipment = $this->_initShipment($IncrementId);

				// If we have a shipment process the tracking and notification
                if ($shipment){
					// Add shipment tracking information
					$this->_addTrack($shipment , $trackNumber);
					// Save all Shipment information
    	            $this->_saveShipment($shipment);
					// If the email notification was requested, then let7s send an email to the customer
					if($is_email) $this->_sendEmail($shipment);
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

		// Return the Process Results
        return $processResult;
    }

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
     * @param string|int $number
     * 
     * @throws Exception
     */
    protected function _addTrack($shipment , $number)
    {
		// Get carrier Information
        $carrier = $shipment->getOrder()->getShippingCarrier();
        $carrierCode = $carrier->getCarrierCode();
        $carrierTitle = Mage::getStoreConfig('carriers/'.$carrierCode.'/title', $shipment->getStoreId());
		// Set Tracking Information
        $track = Mage::getModel('sales/order_shipment_track')
            ->setNumber($number)
            ->setCarrierCode($carrierCode)
            ->setTitle($carrierTitle);
        // Add the Tracking Information to the Shipment
		$shipment->addTrack($track);
    }

    /**
     * Add shipment tracking information to the given shipment
     * @param Mage_Sales_Model_Order_Shipment $shipment
     * 
     * @return IrvineSystems_Yamato_Adminhtml_ImportController
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

    /**
     * Send email notification to the customer with the shipping Information
     * 
     * @param Mage_Sales_Model_Order_Shipment $shipment
     */
    protected function _sendEmail($shipment)
    {
 		// try email process
		try {
			// Set Email and update Shipment Status
			$shipment->sendEmail(true)
				->setEmailSent(true)
				->addComment('',1,1)
				->save();

			// History checking not required before Mage 1.6
			if (version_compare(Mage::getVersion(), '1.6.0.0', '>=')) {
				// Update Main History Status
				$historyItem = Mage::getResourceModel('sales/order_status_history_collection')
					->getUnnotifiedForInstance($shipment, Mage_Sales_Model_Order_Shipment::HISTORY_ENTITY_NAME);
				if ($historyItem) {
					$historyItem->setIsCustomerNotified(1);
					$historyItem->save();
				}
			}

        // Catch Core exceptions
		}catch (Mage_Core_Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        // Catch Other exceptions
        }catch (Exception $e) {
            $this->_getSession()->addError($this->__('Cannot send shipment information.'));
			throw new Exception(Mage::helper($this->_code)->__('An error occured. Shipping Information were not Sent. Details: %s' .$e->getMessage()));
        }
    }
}