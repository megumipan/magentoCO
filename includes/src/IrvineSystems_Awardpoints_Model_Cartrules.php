<?php
/*
 * Irvine Systems Award Points
 *
 * NOTICE OF LICENSE
 * This source file is subject to the Revised BSD License which is bundled with
 * this package in the files LICENSE.txt and LICENSE.html.
 *
 * @category    Magento Sale Extension
 * @package        IrvineSystems_AwardPoints
 * @author Irvine Systems, Inc.
 * @copyright Copyright (c) 2015, Irvine Systems, Inc. (http://irvinesystems.co.jp/)
 * @license     Revised BSD License
 */

class IrvineSystems_Awardpoints_Model_Cartrules extends Mage_Rule_Model_Rule
{
    /**
     * Model Constructor
     * 
     */
    public function _construct()
    {
        // Construct parent
        parent::_construct();
        // Initialize cartrules Resource
		// @see /etc/config.xml
        $this->_init('awardpoints/cartrules');
    }

    /**
     * Getter for condition combine model
     * 
	 * @return IrvineSystems_Awardpoints_Model_Cartrule_Condition_Combine
     */
    public function getConditionsInstance()
    {
        return Mage::getModel('awardpoints/cartrule_condition_combine');
    }

    /**
     * Get all points on all valid Cart rules for the given Order
	 * 
	 * @param Varien Object $order Order Data (Optional, if obmitted current Cart will be used)
	 * 
	 * @return int|float amount of Points
     */
    public function getCartRulesPoints($order = null)
    {
		// Check if a order has been given
        if ($order == null){
			// If not instance the current Shopping Cart
            $order = Mage::getSingleton('checkout/cart');
        }

		// Define and reset points counter
        $points = 0;

        // Get needed Ids
		$storeId = Mage::app()->getStore()->getId();
        $websiteId = Mage::app()->getStore($storeId)->getWebsiteId();
        $customerGroupId = Mage::getSingleton('customer/session')->getCustomerGroupId();

        // get the valid rules for the current website id and customer group id
        $rules = Mage::getModel('awardpoints/cartrules')->getCollection()->setValidationFilter($websiteId, $customerGroupId);

		// Process each rule on the collection
        foreach($rules as $rule)
        {
            // If the rule is not active skip it
			if (!$rule->getStatus()) continue;
			//load current rule model
            $rule_validate = Mage::getModel('awardpoints/cartrules')->load($rule->getRuleId());
			// Get process type id
			$procType = Mage::getModel('awardpoints/awardpoints')->getProcessType();
			// Check validation of the rule for the current order
            $isOrderValid = $rule_validate->validate($order);
			// If the order is valid and the rule is a process type add the points to the order points counter
			if ($isOrderValid && $rule_validate->getActionType() == $procType){
                $points += $rule_validate->getPoints();
            }
        }
		// Return the full points collection
        return $points;
    }
}