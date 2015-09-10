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

class IrvineSystems_Awardpoints_Adminhtml_StatsController extends Mage_Adminhtml_Controller_Action
{
    /**
     * Controller Initialization
     * 
     * @return IrvineSystems_Awardpoints_Adminhtml_StatsController
     */
	protected function _initAction() {
		//load the Layout
		$this->loadLayout()
			->_setActiveMenu('awardpoints/stats')
			->_addBreadcrumb(Mage::helper('awardpoints')->__('Statistics'), Mage::helper('awardpoints')->__('Statistics'));

		return $this;
	}

    /**
     * Statistics area entry point
     * Render Layout
     * 
     */
	public function indexAction() {
		// Initialize Action Sets
		$this->_initAction()
			->_addContent($this->getLayout()->createBlock('awardpoints/adminhtml_stats'))
			->renderLayout();
	}

    /**
     * CheckOrdersPoints Event Handler
     * The function will handle the Chek Orders Points event
     * 
     */
	public function checkAction() {
		// Set model for the selecte listing
		$id = $this->getRequest()->getParam('id');
		$model = Mage::getModel('awardpoints/account')->load($id);
            
		// Set the Form Data
		$data = Mage::getSingleton('adminhtml/session')->getFormData(true);

		// Fill the Data Model if available
		if (!empty($data)) {
			$model->setData($data);
		}

		// Update Stats Register
		Mage::register('stats_data', $model);

		// Load Layout and set Block Parameters
		$this->loadLayout();
		$this->_setActiveMenu('awardpoints/stats');
		// Enable Javasript for the Page
		$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
		// Add Layout Content
		$this->_addContent($this->getLayout()->createBlock('awardpoints/adminhtml_stats_check'));
		
		// Render Layout
		$this->renderLayout();
	}
        
    /**
     * Save Orders Points Check Event Handler
     * The function will Update the order points on the given period of time
     * 
     */
	public function savecheckAction(){

		// Check if we have POST information to process
		// This may never happen, but it is better to prevent it
		if (!$this->getRequest()->getPost()) {
				Mage::getSingleton('adminhtml/session')->addError(Mage::helper('awardpoints')->__('Unable to process a full orders checking'));
				$this->_redirect('*/*');
				return;
		}

		// Set the data to process
		$data = $this->getRequest()->getPost();

		// Set "From" Date Models
		$date = Mage::app()->getLocale()->date($data['from'], Zend_Date::DATE_SHORT, null, false);
		$time = $date->getTimestamp();
		$from = Mage::getModel('core/date')->gmtDate(null, $time);
			
		// Set "To" Date Models
		$date = Mage::app()->getLocale()->date($data['ends'], Zend_Date::DATE_SHORT, null, false);
		$time = $date->getTimestamp();
		$to = Mage::getModel('core/date')->gmtDate(null, $time);
		
		// Get Orders Collection for the given Daqte Range
		$orders = Mage::getModel('sales/order')->getCollection()
			->addAttributeToSelect('*')
			->addAttributeToFilter('created_at', array('from' => $from, 'to' => $to))
			->joinAttribute('status', 'order/status', 'entity_id', null, 'left');

		// Set orders and Updates Counter
		$numOfOrders=0;
		$numOfUpdates=0;

		// Process all order in the collection
		foreach ($orders as $order){
			// set the Points Model
			$accountModel = Mage::getModel('awardpoints/account');
			$awardModel = Mage::getModel('awardpoints/awardpoints');

			// Cehck if the current order has already been processed for points and if it is a valid order
			$order_proc = $accountModel->isOrderProcessed($order->getCustomerId(), $order->getIncrementId());
			$order_valid = in_array($order->getStatus(), Mage::getModel('awardpoints/awardpoints')->getOrderValidAddStatus());
			if (!$order_proc && $order_valid){

				// Get all Items from the order
				$items = $order->getAllItems();
                    
				// Reset the Points Counter
				$pointsAmt = 0;

				// Process All Items in The Order
				foreach ($items as $item) {
					// Get the Item Valid Price
					$price = $item->getRowTotal() + $item->getTaxAmount() - $item->getDiscountAmount();
					// Get Point Value
					$pntValue = Mage::getStoreConfig('awardpoints/general/money_points', Mage::app()->getStore()->getId());
					// Add the Item Point to the Total Points for the cart
					$pointsAmt += $pntValue * $price;
				}
				// Get the Order Date
				$dateStart = $order->getCreatedAt();
				// Get the Duration time for the poins
				$duration = Mage::getStoreConfig('awardpoints/general/points_duration');
				// Explode the Date Format to get only Y-m-d in an array
				$div = explode(" ", $dateStart);
				$div = explode("-", $div[0]);

				// Create Points Dates
				$pointsStart = date("Y-m-d", mktime(0, 0, 0, $div[1], $div[2], $div[0]));
				$pointsEnd = date("Y-m-d", mktime(0, 0, 0, $div[1], $div[2] + $duration, $div[0]));
				
				// Set the Data to be saved
				$post = array(
					'store_id'			=> $order->getStoreId(),
					'customer_id'		=> $order->getCustomerId(),
					'points_type'		=> $awardModel->getOrderPointsType(),
					'points_current'	=> $pointsAmt,
					'points_spent'		=> 0,
					'order_id'			=> $order->getIncrementId(),
					'referral_id'		=> NULL,
					'date_start'		=> $pointsStart,
					'date_end'			=> $pointsEnd,
				);
				
				// Update Model and save the data
				$accountModel->setData($post);
				$accountModel->save();
			$numOfUpdates ++;
			}
		
		$numOfOrders ++;
		}
		
		// Set the Returning message for the Admin according to the Results
		if($numOfUpdates > 0){
			Mage::getSingleton('adminhtml/session')
				->addSuccess(Mage::helper('awardpoints')
				->__('Full check was proceed on %d orders.<br/>A total of %d orders were missing in points calculation.<br/>Missing data has been processed and statistic updated', $numOfOrders, $numOfUpdates)
			);
		}else if($numOfOrders > 0){
			Mage::getSingleton('adminhtml/session')
				->addSuccess(Mage::helper('awardpoints')
				->__('Full check was proceed on %d orders.<br/>All Orders were already processed, no updates were performed', $numOfOrders)
			);
		}else{
			Mage::getSingleton('adminhtml/session')
				->addSuccess(Mage::helper('awardpoints')
				->__('No order to be checked where found on the date range provided')
			);
		};
			
		// Update the Form Data
		Mage::getSingleton('adminhtml/session')->setFormData(false);
               
		// Return to the previous page
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
		$fileName = 'AwardPoits_Statistics.csv';
		// render the content in CSV Format
		$content = $this->getLayout()->createBlock('awardpoints/adminhtml_stats_grid')->getCsv();
		// Convert the content format into SJIS-win encoding for Japanese interface
        if (Mage::app()->getLocale()->getLocale() == 'ja_JP') $content = mb_convert_encoding($content, 'SJIS-win', 'auto');
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
        $fileName   = 'AwardPoits_Statistics.xml';
		// render the content in XML Format
        $content    = $this->getLayout()->createBlock('awardpoints/adminhtml_stats_grid')->getXml();
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