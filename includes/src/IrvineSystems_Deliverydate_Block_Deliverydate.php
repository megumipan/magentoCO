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

class IrvineSystems_Deliverydate_Block_Deliverydate extends Mage_Checkout_Block_Onepage_Abstract
{
    /**
     * Block Construction
     * 
     */
	public function __construct()
	{
		// Set the Block Template
		$this->setTemplate('deliverydate/deliverydate.phtml');     
	}
 
    /**
     * Public Getter for Ajax Post URL
     * 
     * NOTE: The '_secure' parameter set to true is needed for guarranty
	 * the same result on secure and unsecure url (http/https)
     */
	public function getPostUrl()
	{
		return Mage::getUrl('deliverydate/index/ajax', array('_secure'=>true)); 
	}
	
    /**
     * Get the Calendar skin css acording to configuration
     * 
     */
	public function getCallendarSkin()
	{
		// Get Skin Full Path
		$path = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_SKIN).'frontend/base/default/css/deliverydate/';
		// Get Configured Skin
		$skin = Mage::getStoreConfig('deliverydate/general/calendar_skin');
		// Return the skin css full path
		Return $path.'calendar-'.$skin.'.css';
	}
}