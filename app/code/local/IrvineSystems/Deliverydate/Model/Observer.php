<?php
/*
 * Irvine Systems Delivery Date Optimum
 *
 * NOTICE OF LICENSE
 * This source file is subject to the Revised BSD License which is bundled with
 * this package in the files LICENSE.txt and LICENSE.html.
 *
 * @category	Magento Catalog Extension
 * @package		IrvineSystems_Deliverydate
 * @author Irvine Systems, Inc.
 * @copyright Copyright (c) 2015, Irvine Systems, Inc. (http://irvinesystems.co.jp/)
 * @license     Revised BSD License
 */
 
class IrvineSystems_Deliverydate_Model_Observer extends Mage_Core_Model_Abstract
{				
	// Shippping Japan Serie Carriers Codes
    protected $_shippingJapanCodes		= array('japanpost','sagawa','seino', 'yamato');

    /**
     * Shipping Japan Series Check
     * Check if the current quote is using a Shipping Japan Method
     *
     * @return bool
     */
    public function hasShippingJapan()
    {
		// Get the current shipping method
		$carrierMethodCode = $this->_getCarrierMethodCode();
		$currentShippingMethod = explode("_", $carrierMethodCode);
		// If a shipping method is not available return false
		if(!$currentShippingMethod) return false;
		// Set the Carrier Code
		$carrierCode = $currentShippingMethod[0];
    	// If the Carrier Code is on the Shipping Japan Extension list return true
    	if (in_array($carrierCode, $this->_shippingJapanCodes)){
    		$this->_currentCarrier = $carrierCode;
    		return true;
    	}
    	// Otherwise return false
    	return false;
    }

    /**
     * Save Delivery Date during Shipping Selection
     * 
     * @params Varien_Event_Observer $observer
     */
	public function saveDeliveryDate($observer)
	{
        // Get the Posted Data
		$postData = $observer->getRequest()->getPost();
		
        // Get all Delivery Date Values
		$dd =  $postData['shipping_delivery_date'];
		$dt =  $postData['shipping_delivery_time'];
		$dc =  $postData['shipping_delivery_comments'];

		// Check if we have values to save
        if(isset($dd)||isset($dt)||isset($dc))
        {
			// Set the saving values
			$deliveryDate		= $this->_getFormatedDate($dd);
			$deliveryTime		= $dt;
			$deliveryComment	= $dc;

			if($this->hasShippingJapan()){
				$carrierMethodCode = $this->_getCarrierMethodCode();
				$currentShippingMethod = explode("_", $carrierMethodCode);
				// Set the Carrier Code
				$carrierCode = $currentShippingMethod[0];
				$slipModel = Mage::getModel($carrierCode.'/slips');
				$deliveryTime = $slipModel->timeIndexToString($dt);
				if(!$deliveryTime) $deliveryTime = $dt;
			}

			// Update the values into the quote
			if($deliveryDate) $observer->getQuote()->getShippingAddress()->setShippingDeliveryDate($deliveryDate); 
			if($deliveryTime) $observer->getQuote()->getShippingAddress()->setShippingDeliveryTime($deliveryTime); 
			if($deliveryComment) $observer->getQuote()->getShippingAddress()->setShippingDeliveryComments($deliveryComment); 
        }
	}

    /**
     * Get and validate Carrier Method Code
     * 
     * @return string Carrier Method (EX: 'carrierCode_methodCode')
     */
	protected function _getCarrierMethodCode()
    {
        // Get the current shipping code
		$quote = Mage::getSingleton('checkout/session')->getQuote();
		return $quote->getShippingAddress()->getShippingMethod();
    }	

    /**
     * Check if the Current method has a specific configuration and return its data
     * 
     * @return array
     */
	protected function _getMethodData($shipMethod)
	{
		// Get all carriers setting from Config
		$methods = Mage::getStoreConfig('deliverydate/carriers/carriers_methods');
		// Stop processing if no data are available
		if (!$methods || $methods =='') return null;
		// Deserialize data
        $methods = unserialize($methods);
		// Check each methods for see if the current is available, if it is retrive its data
		for ($i = 1; $i <= count($methods['method'])-1; $i++) {
		    // if we find a match process the data
			if ($methods['method'][$i] == $shipMethod){
				// Return the Data
				return $methods['hours'][$i];
			}
		}

		// If nothing was found return false
		return null;
	}

    /**
     * Get the Default Delivery Date information from Config
     * 
     */
	protected function _getDefaultData()
	{
		// Return the Data
		return Mage::getStoreConfig('deliverydate/limiter/delivery_times');
	}

    /**
     * Copy delivery date values into the quote
     * 
     * This method is used to prevent loosing the Delivery information
	 * when a order is edited in admin panel
     * 
     * @params Varien_Event_Observer $observer
     */
	public function copyDesiredDeliveryTimeToQuote($observer)
	{
		// Set the values in the order
		$deliveryDate		= $observer->getOrder()->getDesiredDeliveryDate();
		$deliveryTime		= $observer->getOrder()->getShippingDeliveryTime();
		$deliveryComment	= $observer->getOrder()->getShippingDeliveryComments();

		// Update the values into the quote
		$observer->getQuote()->getShippingAddress()->setShippingDeliveryDate($deliveryDate); 
		$observer->getQuote()->getShippingAddress()->setShippingDeliveryTime($deliveryTime); 
		$observer->getQuote()->getShippingAddress()->setShippingDeliveryComments($deliveryComment); 
	}

    /**
     * Get the Formatted Delivery Date
     * 
     */
	protected function _getFormatedDate($date)
	{
		// Check if the given Value is empty, null oras 0 values
		if(empty($date) ||$date == null || $date == '0000-00-00'){
			// if so stop processing the date
			return null;
		}
		// Format the Date
		$formatedDate = date('Y-m-d',strtotime($date));

		//Return the Formatted Date
		return $formatedDate;
	}
}