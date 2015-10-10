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

class IrvineSystems_Awardpoints_Model_Cartrule_Condition_Combine extends Mage_Rule_Model_Condition_Combine
{
    /**
     * Model Constructor
     * 
     */
    public function __construct()
    {
        // Construct parent
		parent::__construct();
        // Set current type
        $this->setType('awardpoints/cartrule_condition_combine');
    }

    /**
     * Getter for New Child Option
	 * Add custom condition Sets to main Condition Array
     * 
     * @return array
     */
    public function getNewChildSelectOptions()
    {
		// Get Conditions
        $conditions = parent::getNewChildSelectOptions();

		// Merge New Label with all Conditions
        $conditions = array_merge_recursive($conditions, array(array(
			'value'	=>'awardpoints/cartrule_condition_combine',
			'label'	=>Mage::helper('awardpoints')->__('Conditions Combination')
			)));

        // Set User Location Array
		$c_attributes = array(
            array('value'=>'awardpoints/cartrule_condition_customeraddress_params|postcode', 'label'=>Mage::helper('awardpoints')->__('Customer post code')),
            array('value'=>'awardpoints/cartrule_condition_customeraddress_params|region_id', 'label'=>Mage::helper('awardpoints')->__('Customer region')),
            array('value'=>'awardpoints/cartrule_condition_customeraddress_params|country_id', 'label'=>Mage::helper('awardpoints')->__('Customer country'))
        );

		// Merge User Location array with all Conditions
        $conditions = array_merge_recursive($conditions, array(
            array('label'=>Mage::helper('awardpoints')->__('Customer location'), 'value'=>$c_attributes),
        ));

		// Get Address Condition Module
        $addressCondition = Mage::getModel('salesrule/rule_condition_address');
		// Get address Attributes
        $addressAttributes = $addressCondition->loadAttributeOptions()->getAttributeOption();
		// Set all cart Attributes
        $cart_attributes = array();
        foreach ($addressAttributes as $code=>$label) {
            $cart_attributes[] = array('value'=>'salesrule/rule_condition_address|'.$code, 'label'=>$label);
        }

		// Merge Cart Attributes array with all Conditions
        $conditions = array_merge_recursive($conditions, array(
            array('label'=>Mage::helper('awardpoints')->__('Cart Attributes'), 'value'=>$cart_attributes),
        ));

		// Return Completed Consitions set
        return $conditions;
    }

    /**
     * Update HTML information
	 * 
     * 
     * @return string Updated HTML
     */
    public function asHtml()
    {
		// Update Condition Label
        $html = $this->getTypeElement()->getHtml().
            Mage::helper('awardpoints')->__("If %s of these order conditions are %s",
              $this->getAggregatorElement()->getHtml(),
			  $this->getValueElement()->getHtml()
           );
           // Remove the link if we are in main selection
		   if ($this->getId()!='1') {
               $html.= $this->getRemoveLinkHtml();
           }

        return $html;
    }
}