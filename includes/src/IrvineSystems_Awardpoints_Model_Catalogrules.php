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

class IrvineSystems_Awardpoints_Model_Catalogrules extends Mage_Rule_Model_Rule
{
    /**
     * Model Constructor
     * 
     */
    public function _construct()
    {
        // Construct parent
        parent::_construct();
        // Initialize catalogrules Resource
		// @see /etc/config.xml
        $this->_init('awardpoints/catalogrules');
    }

    /**
     * Getter for condition combine model
     * 
	 * @return IrvineSystems_Awardpoints_Model_Catalogrule_Condition_Combine
     */
    public function getConditionsInstance()
    {
        return Mage::getModel('awardpoints/catalogrule_condition_combine');
    }

    /**
     * Get all points on all valid catalog rules for the given Product
	 * 
	 * @param Varien Object $product Product Data
	 * 
	 * @return int|float amount of Points
     */
    public function getCatalogRulesPoints($product)
    {
		// Define and reset points counter
        $points = 0;

        // Get needed Ids
        $storeId = Mage::app()->getStore()->getId();
        $websiteId = Mage::app()->getStore($storeId)->getWebsiteId();
        $customerGroupId = Mage::getSingleton('customer/session')->getCustomerGroupId();

        // get the valid rules for the current website id and customer group id
        $rules = Mage::getModel('awardpoints/catalogrules')->getCollection()->setValidationFilter($websiteId, $customerGroupId);

		// Process each rule on the collection
        foreach($rules as $rule)
        {
            // If the rule is not active skip it
            if (!$rule->getStatus()) continue;
			//load current rule model
            $rule_validate = Mage::getModel('awardpoints/catalogrules')->load($rule->getRuleId());
			// return noprocess if we dont need to process the points
			$noProcType = Mage::getModel('awardpoints/awardpoints')->getNoProcessType();
			if ($rule_validate->getActionType() == $noProcType) return 'no_proc';

			// Get process type id
			$procType = Mage::getModel('awardpoints/awardpoints')->getProcessType();
			// Check validation of the rule for the current product
            $isProductValid = $rule_validate->validate($product);
			// If the order is valid and the rule is a process type add the points to the order points counter
			if ($isProductValid && $rule_validate->getActionType() == $procType){
                $points += $rule_validate->getPoints();
            };
        }
		// Return the full points collection
		return $points;
    }
}