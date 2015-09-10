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

class IrvineSystems_Awardpoints_Adminhtml_CustomertpointsController extends Mage_Adminhtml_Controller_Action
{
    /**
     * Controller Initialization
     * 
     * @return IrvineSystems_Awardpoints_Adminhtml_StatsController
     */
	protected function _initAction() {
		//load the Layout
		$this->loadLayout()
			->_setActiveMenu('awardpoints/customertpoints')
			->_addBreadcrumb(Mage::helper('awardpoints')->__('Customers Points'), Mage::helper('awardpoints')->__('Customers Points'));

		return $this;
	}

    /**
     * Customers Points area entry point
     * Render Layout
     * 
     */
	public function indexAction() {
		// Initialize Action Sets
		$this->_initAction()
			->_addContent($this->getLayout()->createBlock('awardpoints/adminhtml_customertpoints'))
			->renderLayout();
	}

    /**
     * New Listing Event Handler
     * The function will handle the New listing event
     * 
     */
	public function newAction() {

		// Load model data for the selected listing
		$id = $this->getRequest()->getParam('id');
		$model  = Mage::getModel('awardpoints/account')->load($id);

		// Set the Form Data
		$data = Mage::getSingleton('adminhtml/session')->getFormData(true);
		$model->setData($data);

		// Update Stats Register
		Mage::register('stats_data', $model);

		// Load Layout and set Block Parameters
		$this->loadLayout();
		$this->_setActiveMenu('awardpoints/stats');

		// Enable Javasript for the Page
		$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

		// Add Layout Content
		$this->_addContent($this->getLayout()->createBlock('awardpoints/adminhtml_customertpoints_new'));

		// Render Layout
		$this->renderLayout();
	}

    /**
     * Edit Listing Event Handler
     * The function will handle the Edit listing event
     * 
     */
	public function editAction() {

		// Load model data for the selected listing
		$id = $this->getRequest()->getParam('id');
		$model  = Mage::getModel('awardpoints/account')->load($id);

		// Set the Form Data
		$data = Mage::getSingleton('adminhtml/session')->getFormData(true);
		$model->setData($data);

		// Update Stats Register
		Mage::register('stats_data', $model);

		// Load Layout and set Block Parameters
		$this->loadLayout();
		$this->_setActiveMenu('awardpoints/stats');
		// Enable Javasript for the Page
		$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

		// Add Layout Content
		$this->_addContent($this->getLayout()->createBlock('awardpoints/adminhtml_customertpoints_edit'));

		// Render Layout
		$this->renderLayout();
	}

    /**
     * Save Points Event Handler
     * The function will Save the newly added or edited points for the client
     * 
     */
	public function saveAction() {

		// Check if we have POST information to process
		// This may never happen, but it is better to prevent it
		if (!$this->getRequest()->getParams()) {
				Mage::getSingleton('adminhtml/session')->addError(Mage::helper('awardpoints')->__('Points data to be saved were not found. Please refresh the page and try again'));
				$this->_redirect('*/*');
				return;
		}

		// Set the data Array
		$data = $this->getRequest()->getParams();
		// Get Points Model
		$model = Mage::getModel('awardpoints/account');
		// Update Model Data
		$model->setData($data)->setId($data['id']);
		// if we are adding or editing a specific client set the id in custoemr_id
		$cId = null;
		if(isset($data['cId'])){
			$cId = $data['cId'];
			$model->setCustomerId($cId);
		}

		// Save the Model Data
		$model->save();
		// Inform the admin Panel for the success
		Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('awardpoints')->__('Points were successfully saved'));
		// Reset the Form
		Mage::getSingleton('adminhtml/session')->setFormData(false);

		// If we are in Customer specific editing, restore collection for that client
		if ($cId){
			$this->_redirect('*/*/', array('cId' => $cId));
			return;
		}
		
		// Otherwise go back to full Clients list
		$this->_redirect('*/*/');
		return;
	}

    /**
     * Delete Points Event Handler
     * The function will handle the Delete listing event
     * 
     */
	public function deleteAction() {

		// Get request id
		$id = $this->getRequest()->getParam('id');
		// Get customer id (if any)
		$cId = $this->getRequest()->getParam('cId');
		// Get Points Model
		$model = Mage::getModel('awardpoints/account');
		// Delete the requested id from the Model
		$model->setId($id)->delete();
		// Inform the admin Panel for the success
		Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('awardpoints')->__('Points were successfully deleted'));
		
		// If we are in Customer specific editing, restore collection for that client
		if ($cId){
			$this->_redirect('*/*/', array('cId' => $cId));
			return;
		}
		
		// Otherwise go back to full Clients list
		$this->_redirect('*/*/');
		return;
	}

    /**
     * MassDelete Event Handler
     * The function will handle the MassDelete event
     * 
     */
    public function massDeleteAction() {
		// Get all selected Id to be deleted
        $ruleIds = $this->getRequest()->getParam('account_ids');

		// Get customer id (if any)
		$cId = $this->getRequest()->getParam('cId');

		// Get Points Model
		$model = Mage::getModel('awardpoints/account');

		// This may never happen, but it is better to prevent it
		if(!is_array($ruleIds)) {
				Mage::getSingleton('adminhtml/session')->addError(Mage::helper('awardpoints')->__('No points were selected. Please select the points to be deleted.'));
				$this->_redirect('*/*');
				return;
		}

		// Delete each Listing in the selection
		foreach ($ruleIds as $ruleId) {
			$rule = $model->load($ruleId);
			$rule->delete();
		}
             
		// Inform the admin Panel for the success
		Mage::getSingleton('adminhtml/session')->addSuccess(
			Mage::helper('awardpoints')->__(
				'A Total of %d points data were successfully deleted', count($ruleIds)
			)
		);

		// If we are in Customer specific editing, restore collection for that client
		if ($cId){
			$this->_redirect('*/*/', array('cId' => $cId));
			return;
		}
		
		// Otherwise go back to full Clients list
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
		$fileName = 'AwardPoits_CustomerPoints.csv';
		// render the content in CSV Format
		$content = $this->getLayout()->createBlock('awardpoints/adminhtml_customertpoints_grid')->getCsv();
		// Convert the content format into SJIS-win encoding for Japanese interface
        if (Mage::app()->getLocale()->getLocale() == 'ja_JP') $content = mb_convert_encoding($content, 'SJIS-win', 'auto');
		// Start the Download
        $this->_sendUploadResponse($fileName, $content);
    }

    /**
     * Export to XML File Event Handler
     * The function will export all data in the collection in a XML File
     * 
     */
    public function exportXmlAction()
    {
		// Set name for the file
        $fileName = 'AwardPoits_CustomerPoints.xml';
		// render the content in XML Format
        $content = $this->getLayout()->createBlock('awardpoints/adminhtml_customertpoints_grid')->getXml();
		// Convert the content format into SJIS-win encoding for Japanese interface
        if (Mage::app()->getLocale()->getLocale() == 'ja_JP') $content = mb_convert_encoding($content, 'SJIS-win', 'auto');
		// Start the Download
        $this->_sendUploadResponse($fileName, $content);
    }

    /**
     * Prepare file download response
     *
     * @Deprecated function
     * keep the bridge for backward compatibility
     *
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