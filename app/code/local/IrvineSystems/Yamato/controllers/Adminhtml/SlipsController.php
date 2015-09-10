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

class IrvineSystems_Yamato_Adminhtml_SlipsController extends Mage_Adminhtml_Controller_Action
{
    /**
     * Controller Initialization
     * 
     * @return IrvineSystems_Yamato_Adminhtml_SlipsController
     */
	protected function _initAction()
	{
		//load the Layout
		$this->loadLayout()
			->_setActiveMenu('sales')
			->_addBreadcrumb(Mage::helper('yamato')->__('Shipping Slips'), Mage::helper('yamato')->__('Shipping Slips'));

		return $this;
	}

    /**
     * Slips area entry point
     * Render Layout
     * 
     */
	public function indexAction()
	{
		// Initialize Action Sets
		$this->_initAction()
			->_addContent($this->getLayout()->createBlock('yamato/adminhtml_slips'))
			->renderLayout();
	}

    /**
     * Clean Slips Database
     * 
     */
	public function cleanAction()
	{
		// Order statuses which can be deleted
		$status = "complete','canceled','closed";
		// Get Slips Model
		$model = Mage::getModel('yamato/slips');
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
				->addSuccess(Mage::helper('yamato')
				->__('Full check was proceed on %d slips.<br/>A total of %d slips data were removed from the database.', $numOfSlips, $numOfUpdates)
			);
		}else if($numOfSlips > 0){
			Mage::getSingleton('adminhtml/session')
				->addSuccess(Mage::helper('yamato')
				->__('Full check was proceed on %d slips.<br/>No deletable slips were found.', $numOfSlips)
			);
		}else{
			Mage::getSingleton('adminhtml/session')
				->addSuccess(Mage::helper('yamato')
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
		$model = Mage::getModel('yamato/slips');

		// Errror control if a slip get erased after beign selected
		if ($id) {
            $model->load($id);
            if (! $model->getSlipId()) {
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('yamato')->__('This slip no longer exists'));
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
        $this->_setActiveMenu('yamato');
        $block = $this->getLayout()->createBlock('yamato/adminhtml_slips_edit')
            ->setData('action', $this->getUrl('*/yamato_slips/save'));

		// Enable Javasript for the Page
        $this->getLayout()->getBlock('head')
            ->setCanLoadExtJs(true)
            ->setCanLoadRulesJs(true);

		// Add main and Tabs content to layout
		$this->_addContent($block)
			->_addLeft($this->getLayout()->createBlock('yamato/adminhtml_slips_edit_tabs'))
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
				Mage::getSingleton('adminhtml/session')->addError(Mage::helper('yamato')->__('Slip data to be saved were not found. Please refresh the page and try again'));
				$this->_redirect('*/*');
				return;
		}

		// Set the data Array
		$data = $this->getRequest()->getParams();
		// Get Slips Model
		$model = Mage::getModel('yamato/slips');

		//Reference to slip Id
		$slipId = null;
		if (isset($data['slip_id'])) $slipId = $data['slip_id'];
		
		// If the Slip id is missing stopr processing and go back to Grid
		if (empty($slipId)){
			Mage::throwException(Mage::helper('yamato')->__('No slips were specified. Please check the Slip Id'));
               $this->_redirect('*/*/');
			return;
		}
		
		// Load the current Slip into the model
		$model->load($slipId);
		// Check if by any change the ids are not matching
		if ($slipId != $model->getSlipId()) {
			Mage::throwException(Mage::helper('yamato')->__('Wrong slip specified.'));
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
		Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('yamato')->__('Slip was successfully saved'));
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
		// Set name for the file
		$fileName = 'Yamato_Slips.csv';
		
		// Order statuses which can be deleted
		$status = "processing";
		// Get Slips Model
		$model = Mage::getModel('yamato/slips');
		// Get Slips Model
		$slips = $model->getCollection()->addOrderStatusFilter($status);
		// Delete each Listing in the selection
		foreach ($slips as $slip) {
			$slipData = $model->load($slip->getSlipId());
			$today = date("Y/m/d");
			$slipData->setShipmentDate($today); 
			$model->save();
		}
		
		// render the content in CSV Format
		$content = $this->getLayout()->createBlock('yamato/adminhtml_slips_pgrid')->getCsv();
		// Convert the content format into SJIS-win encoding
        $content = mb_convert_encoding($content, 'SJIS-win', 'auto');
		// Normalize format to CRLF
		$content = str_replace("\r\n", "\n", $content);
		$content = str_replace(array("\r", "\n"), "\r\n", $content); 
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
		// Set name for the file
        $fileName   = 'Yamato_Slips.xml';
        
        // Order statuses which can be deleted
        $status = "processing";
        // Get Slips Model
        $model = Mage::getModel('yamato/slips');
        // Get Slips Model
        $slips = $model->getCollection()->addOrderStatusFilter($status);
        // Delete each Listing in the selection
        foreach ($slips as $slip) {
        	$slipData = $model->load($slip->getSlipId());
        	$today = date("Y/m/d");
        	$slipData->setShipmentDate($today);
        	$model->save();
        }
        
		// render the content in XML Format
        $content    = $this->getLayout()->createBlock('yamato/adminhtml_slips_pgrid')->getXml();
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
}