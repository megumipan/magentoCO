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

class IrvineSystems_Deliverydate_Helper_Data extends Mage_Core_Helper_Abstract{
	
	// Shippping Japan Serie Carriers Codes
    protected $_shippingJapanCodes		= array('japanpost','sagawa','seino', 'yamato');

    /**
     * Shipping Japan Series Check
     * Check if the current quote is using a Shipping Japan Method
     *
     * @return bool
     */
    public function hasShippingJapan($currentShippingMethod = null)
    {
		// Get the current shipping method if none were passed
		if(!$currentShippingMethod) $currentShippingMethod = $this->getCurrentShippingMethod();
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
     * Get the Current shipping Carrier/Method Selected
     * Returned Structure:
     * result[0] = Carrier Code
     * result[1] = Method Code
     *
     * @param $quote Mage_Sales_Model_Quote
     *
     * @return array
     */
    public function getCurrentShippingMethod($quote = null)
    {
		// If the quote instance is not available get the current session instance
		if (!$quote) $quote = Mage::getSingleton('checkout/session')->getQuote();

		$hippingMethod = $quote->getShippingAddress()->getShippingMethod();
		if (!$hippingMethod) return null;
    	// Split the shipping method code into an array
    	$code = explode("_",$quote->getShippingAddress()->getShippingMethod());
		// Return the Codes
		return $code;
    }	
	
}