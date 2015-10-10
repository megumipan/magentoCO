<?php
/*
 * Irvine Systems Shipping Japan Jp
 *
 * NOTICE OF LICENSE
 * This source file is subject to the Revised BSD License which is bundled with
 * this package in the files LICENSE.txt and LICENSE.html.
 *
 * @category	Magento Shipping Extension
 * @package		IrvineSystems_JapanPost
 * @author Irvine Systems, Inc.
 * @copyright Copyright (c) 2015, Irvine Systems, Inc. (http://irvinesystems.co.jp/)
 * @license     Revised BSD License
 */

class IrvineSystems_JapanPost_Block_Japanpost extends Mage_Checkout_Block_Onepage_Abstract
{
    /**
     * Block Construction
     * 
     */
	public function __construct()
	{
		// Set the Block Template
		$this->setTemplate('japanpost/japanpost.phtml');     
	}

    /**
     * Public Getter for Ajax Post URL
     * 
     * NOTE: The '_secure' parameter set to true is needed for guarranty
	 * the same result on secure and unsecure url (http/https)
     */
	public function getPostUrl()
	{
		return Mage::getUrl('japanpost/index/ajax', array('_secure'=>true)); 
	}
}