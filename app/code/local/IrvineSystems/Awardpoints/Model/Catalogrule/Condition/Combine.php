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

class IrvineSystems_Awardpoints_Model_Catalogrule_Condition_Combine extends Mage_Rule_Model_Condition_Combine
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
        $this->setType('awardpoints/catalogrule_condition_combine');
    }

    /**
     * Getter for New Child Option
	 * Add custom condition Sets to main Condition Array
     * 
     * @return array
     */
    public function getNewChildSelectOptions()
    {
        // Get Product Attributes
        $productAttributes = Mage::getModel('catalogrule/rule_condition_product')
			->loadAttributeOptions()
			->getAttributeOption();
        // Define Attributes Array
		$attributes = array();
        // get and update all product attributes
        foreach ($productAttributes as $code=>$label) {
            $attributes[] = array('value'=>'catalogrule/rule_condition_product|'.$code, 'label'=>$label);
        }
		// Get parent Conditions
        $conditions = parent::getNewChildSelectOptions();
		// Merge the new condition
        $conditions = array_merge_recursive($conditions, array(
            array('value'=>'awardpoints/catalogrule_condition_combine', 'label'=>Mage::helper('awardpoints')->__('Conditions Combination')),
            array('label'=>Mage::helper('awardpoints')->__('Product Attribute'), 'value'=>$attributes),
        ));
		// return the conditions
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

    /**
     * Validate the collection Attributes
	 * 
     * 
     * @return Varien Object
     */
    public function collectValidatedAttributes($productCollection)
    {
		// Check all conbditions
        foreach ($this->getConditions() as $condition) {
            $condition->collectValidatedAttributes($productCollection);
        }
        return $this;
    }
}