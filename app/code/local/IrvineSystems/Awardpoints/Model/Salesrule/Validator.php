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

class IrvineSystems_Awardpoints_Model_Salesrule_Validator extends Mage_SalesRule_Model_Validator
{
    /**
     * Mage_SalesRule_Model_Validator Process Override
     * 
     * @param Mage_Sales_Model_Quote_Item_Abstract $item
     * 
     * @return IrvineSystems_Awardpoints_Model_Salesrule_Validator
     * 
     */
	public function process(Mage_Sales_Model_Quote_Item_Abstract $item)
	{
		// Execute parent processing
		parent::process($item);
		// Exit if shopping points are not enable
		if (!Mage::getModel('awardpoints/awardpoints')->getConfig('shopping_show')) return $this;
		// Get Customer Session
		$customer = Mage::getSingleton('customer/session');
		// Do not process if the customer is not logged in
		if (!$customer->isLoggedIn()) return $this;
		// Get Customer and Store IDs
		$customerId = $customer->getCustomerId();
		$storeId = Mage::app()->getStore()->getId();
		// Get points Model
		$model = Mage::getModel('awardpoints/account');
		// Check if is auto Use Points Mode
		$autoUse = Mage::getModel('awardpoints/awardpoints')->getConfig('auto_use');
		if ($autoUse){
			// Get Customer and Credit Points
			$customerPoints = $model->getPointsCurrent($customerId, $storeId);
			$creditPoints = Mage::helper('awardpoints/event')->getCreditPoints();
			if ($customerPoints && $customerPoints > $creditPoints){
				// Get Car Points Amount
				$cartPoints = Mage::getModel('awardpoints/discount')->getCartAmount();
				$cartPoints = Mage::helper('awardpoints/data')->processMathValue($cartPoints);
				// Get the amount of points to be used
				$pointsValue = min(Mage::helper('awardpoints/data')->convertMoneyToPoints($cartPoints), (int)$customerPoints);
				// Update customer Session
				Mage::getSingleton('customer/session')->setProductChecked(0);
				// Update customer Points
				Mage::helper('awardpoints/event')->setCreditPoints($pointsValue);
			}
		}
		// Apply the points
		Mage::getModel('awardpoints/discount')->apply($item);
		// Return
		return $this;
	}
}