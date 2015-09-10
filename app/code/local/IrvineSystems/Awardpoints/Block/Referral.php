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

class IrvineSystems_Awardpoints_Block_Referral extends Mage_Core_Block_Template
{
    /**
     * Block Constructor
     * 
     */
    public function __construct()
    {
        // Parent Construction
        parent::__construct();
        // Set the new Template
        $this->setTemplate('referafriend/referral.phtml');
		// Get the Resource Model
		$resource = Mage::getResourceModel('awardpoints/referral_collection');
		// Filter the resource by Customer Id
        $referred = $resource->addCustomerFilter($this->getCustomerId());
		// Return the Points Information
        $this->setReferred($referred);
    }

    /**
     * Prepare Layout
     * 
     */
    public function _prepareLayout()
    {
		// Prepare parent Layout
        parent::_prepareLayout();
		// Set Pager Blacok with the points collection
        $pager = $this->getLayout()->createBlock('page/html_pager', 'awardpoints.referral')
            ->setCollection($this->getReferred());
		// Set the Pager Child
        $this->setChild('pager', $pager);
		// Load the Points
        $this->getReferred()->load();
		// Return the updates layout
        return $this;
    }

    /**
     * Get the Pager Html
     * 
     */
    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }

    /**
     * Get the Referring URL
     * 
     * @return string
     */
    public function getReferringUrl()
    {
        // Get the Customer User Id
		$userId = $this->getCustomerId();
        // Set the permanent referral Link
        return $this->getUrl('awardpoints/index/invitation')."referrer/".$userId;
    }

    /**
     * Check if permanent link is enable
     * 
     * @return bool
     */
    public function isPermanentLink()
    {
		// If show permanent link is enable, return true
		if (Mage::getModel('awardpoints/awardpoints')->getConfig('referral_permanent') == 1) return true;
		// otherwise false
        return false;
    }

    /**
     * Check if Email Referral is enable
     * 
     * @return bool
     */
    public function isEmailReferral()
    {
		// If show permanent link is enable, return true
		if (Mage::getModel('awardpoints/awardpoints')->getConfig('referral_enable') == 1) return true;
		// otherwise false
        return false;
    }

    /**
     * Check if AddThis is enable
     * 
     * @return bool
     */
    public function isAddthis()
    {
		// If addThis is enable, return true
		if (Mage::getModel('awardpoints/awardpoints')->getConfig('referral_addthis') == 1) return true;
		// otherwise false
        return false;
    }

    /**
     * Getter for Customer Id
     * 
     * @return int
     */
    public function getCustomerId() {
        return Mage::getModel('customer/session')->getCustomerId();
    }

    /**
     * Getter for referrer Points Value
     * 
     * @return int
     */
    public function getReferrerPoints()
    {
        return Mage::getModel('awardpoints/awardpoints')->getConfig('referral_points');
    }

    /**
     * Getter for Masimum order Quantity
     * 
     * @return int
     */
    public function getMaximumOrder()
    {
		// Get order Mode information
		$orderMode		= Mage::getModel('awardpoints/awardpoints')->getConfig('referral_orders_mode');
		$orderNumber	= Mage::getModel('awardpoints/awardpoints')->getConfig('referral_orders_number');
		$unlimited		= Mage::helper('awardpoints')->__('Unlimited');

		// Return specific Values for unlimited and custom amounts
		switch ($orderMode) {
		    case 2: return $unlimited;
		    case 3: return $orderNumber;
		}

		// Return 1 for first order only
		return 1;
    }

    /**
     * Getter for Parent Order Points
     * 
     * @return int
     */
    public function getParentOrderPoints()
    {
        return Mage::getModel('awardpoints/awardpoints')->getConfig('referral_parent_order');
    }

    /**
     * Getter for Child Order Points
     * 
     * @return int
     */
    public function getChildOrderPoints()
    {
        return Mage::getModel('awardpoints/awardpoints')->getConfig('referral_child_order');
    }

    /**
     * Getter for the Pager Html
     * 
     */
    public function getStatuslabel($id)
    {
		$refStatuses = Mage::getModel('awardpoints/awardpoints')->getReferralStatusTypes();
        return $refStatuses[$id];
    }
}