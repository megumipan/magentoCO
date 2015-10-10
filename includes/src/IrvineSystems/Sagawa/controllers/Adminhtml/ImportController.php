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

class IrvineSystems_Sagawa_Adminhtml_ImportController extends IrvineSystems_Sagawa_Controller_Abstract
{
	// CSV columns map
	protected $_arraymap = array(
	'id_001','id_002','customer_address_code','customer_tel','customer_postcode','customer_address_1','customer_address_2','customer_address_3','id_009','customer_name',
	'customer_namekana','id_012','customer_memberid','customer_id','shipper_tel','id_016','id_017','id_018','id_019','id_020','store_contact','Id_022','store_tel',
	'store_postcode','store_address_1','store_address_2','store_name','store_namekana','packing_code','product_name_1','product_name_2','product_name_3','product_name_4',
	'order_id', // The Field 'product_name_5' Used for save Order Id
	'packages_number','ship_method','cooling_shipment','delivery_date','delivery_time','delivery_time_min','cod_amount','tax_amount','cod_method','ensured_amount',
	'ensured_amount_printed','service_code_1','service_code_2','service_code_3','delivery_type','src_class','branc_code','payment_source','id_053');

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
			throw new Exception(Mage::helper($this->_code)->__('An error occurred. Shipping Information were not Sent. Details: %s' .$e->getMessage()));
        }
    }
}