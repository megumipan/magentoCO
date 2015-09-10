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

class IrvineSystems_Awardpoints_Model_Cartrule_Condition_Customerattribute_Params extends Mage_Rule_Model_Condition_Abstract
{
    /**
     * Model Constructor
     * 
     */
    public function __construct()
    {
        // Construct parent
        parent::__construct();
        // Set current type and reset Values
        $this->setType('awardpoints/cartrule_condition_customerattribute_params')
            ->setValue(null);
    }

    /**
     * Set the Attribute Options to be loaded
	 * Add custom condition Sets to main Condition Array
     * 
     * @return IrvineSystems_Awardpoints_Model_Cartrule_Condition_Customeraddress_Params
     */
    public function loadAttributeOptions()
    {
		// Load the customer Model
        $customer = Mage::getModel('customer/customer');
		// Build Attributes Array
		$attributes = array();
		// Check all customer attributes and add all valid to the array
        foreach ($customer->getAttributes() as $attribute){
            if ($attribute->getBackendModel() == '' && $attribute->getFrontendLabel() != ''){
                $attributes[$attribute->getAttributeCode()] = $attribute->getFrontendLabel();
            }
        }
		// Set the Attributes
        $this->setAttributeOption($attributes);        
		// Return the result
        return $this;
    }

    /**
     * Set the Operators Options to be loaded
	 * Add custom condition Sets to main Condition Array
     * 
     * @return IrvineSystems_Awardpoints_Model_Cartrule_Condition_Customeraddress_Params
     */
    public function loadOperatorOptions()
    {
		// Set the Helper definition
		$helper = Mage::helper('awardpoints');
		// Build Operators Array
		$operators = array(
            '=='  => $helper->__('is'),
            '!='  => $helper->__('is not'),
            '>='  => $helper->__('equals or greater than'),
            '<='  => $helper->__('equals or less than'),
            '>'   => $helper->__('greater than'),
            '<'   => $helper->__('less than'),
        );
		// Set the Attributes
        $this->setOperatorOption($operators);        
		// Return the result
        return $this;
    }

    /**
     * Retrieve Explicit Apply
     *
     * @return bool
     */
    public function getExplicitApply()
    {
        // Return true for Sku and Category ID
        switch ($this->getAttribute()) {
            case 'sku': case 'category_ids':
                return true;
        }

        // Return true for valid customer attribute date and datetime
        $customer = Mage::getModel('customer/customer');
        foreach ($customer->getAttributes() as $attribute){
            if ($attribute->getBackendModel() == "" && $attribute->getFrontendLabel() != ""){
                if ($attribute->getAttributeCode() == $this->getAttribute()){
                    switch ($attribute->getBackendType()) {
                        case 'date':
                        case 'datetime':
                            return true;
                    }
                }
            }
        }
        // Otherwise return false
        return false;
    }

    /**
     * Retrieve value element
     *
     * @return Varien_Data_Form_Element_Abstract
     */
    public function getValueElement()
    {
		// Get Abstarct Value Element
        $element = parent::getValueElement();

        // Add the calendar icon to customer date and datetime attributes
        $customer = Mage::getModel('customer/customer');
        foreach ($customer->getAttributes() as $attribute){
            if ($attribute->getBackendModel() == "" && $attribute->getFrontendLabel() != ""){
                if ($attribute->getAttributeCode() == $this->getAttribute()){
                    switch ($attribute->getBackendType()) {
                        case 'date':
                        case 'datetime':
                            $element->setImage(Mage::getDesign()->getSkinUrl('images/grid-cal.gif'));
                            break;
                    }
                }
            }
        }
        // Return updated element
        return $element;
    }

    /**
     * Retrieve value element chooser URL
     *
     * @return string
     */
    public function getValueElementChooserUrl()
    {
        // Defife $url
		$url = '';
		// Compose the url for sku and category id
        switch ($this->getAttribute()) {
            case 'sku': case 'category_ids':
				// Set the main url
                $url = 'adminhtml/promo_widget/chooser/attribute/'.$this->getAttribute();
				// Check if there is Javascript to be added
                if ($this->getJsFormObject()) {
                    $url .= '/form/'.$this->getJsFormObject();
                }
				$url = Mage::helper('adminhtml')->getUrl($url);
                break;
        }
		// return the url
        return $url;
    }

    /**
     * Retrieve html code
     *
     * @return string
     */
    public function asHtml()
    {
		// Check if the Attribute is a sku, and format the HTML
        if ($this->getAttribute()=='sku')
        {
			// Format html
			$html = $this->getTypeElement()->getHtml().
				Mage::helper('awardpoints')->__("%s %s",
				$this->getAttributeElement()->getHtml(),
				$this->getValueElement()->getHtml());
			// Remove the link if we are in main selection
			if ($this->getId()!='1') {
				$html.= $this->getRemoveLinkHtml();
			}
			// return the html
			return $html;
        }
		// If the Attribute is not a sku return the abstract Html
        return parent::asHtml();
    }

    /**
     * Retrieve the Imput type
     *
     * @return string
     */
    public function getInputType()
    {
		// Switch between the Cart Attributes type and return the correct input type
        switch ($this->getAttribute()) {
            case 'base_subtotal':
			case 'weight':
			case 'total_qty':
                return 'numeric';
            case 'shipping_method':
			case 'payment_method':
			case 'country_id':
			case 'region_id':
			case 'group_id':
                return 'select';
        }

		// Switch between the Customer Attributes type and return the correct input type
        $customer = Mage::getModel('customer/customer');
        foreach ($customer->getAttributes() as $attribute){
            if ($attribute->getBackendModel() == "" && $attribute->getFrontendLabel() != ""){
                if ($attribute->getAttributeCode() == $this->getAttribute()){
                    switch ($attribute->getBackendType()) {
                        case 'date':
                        case 'datetime':
                            return 'date';
                        case 'varchar':
                            return 'string';
                        case 'int':
                            return 'numeric';
                        case 'static':
                            return 'select';
                    }
                }
            }
        }
        // All other return string
        return 'string';
    }

    /**
     * Retrieve Attribute element
     *
     * @return Varien Object
     */
    public function getAttributeElement()
    {
        // Get the Abstract attribute element
		$element = parent::getAttributeElement();
        // Set the property
        $element->setShowAsText(true);
        // Return the Element
        return $element;
    }

    /**
     * Retrieve Value Element Type
     *
     * @return Varien Object
     */
    public function getValueElementType()
    {
		// Switch between the Cart Attributes type and return the correct Element type
        switch ($this->getAttribute()) {
            case 'shipping_method':
            case 'payment_method':
            case 'country_id':
            case 'region_id':
            case 'group_id':
                return 'select';
        }

		// Switch between the Customer Attributes type and return the correct Element type
        $customer = Mage::getModel('customer/customer');
        foreach ($customer->getAttributes() as $attribute){
            if ($attribute->getBackendModel() == "" && $attribute->getFrontendLabel() != ""){
                if ($attribute->getAttributeCode() == $this->getAttribute()){
                    switch ($attribute->getBackendType()) {
                        case 'date':
                        case 'datetime':
                            return 'date';
                        case 'text':
                            return 'text';
                        case 'int':
                            return 'numeric';
                    }
                }
            }
        }
        // All other return text
        return 'text';
    }

    /**
     * Retrieve Value Select Options
     *
     * @return Varien Object
     */
    public function getValueSelectOptions()
    {
		// Check if the value_select_options is not available
		if (!$this->hasData('value_select_options')) {
			// Define the options array
			$options = array();
			// Fill the options according to the Attribute type
			switch ($this->getAttribute()) {
				case 'confirmation':
					$options = Mage::getModel('adminhtml/system_config_source_yesno')->toOptionArray();
					break;
				case 'country_id':
					$options = Mage::getModel('adminhtml/system_config_source_country')->toOptionArray();
					break;
				case 'region_id':
					$options = Mage::getModel('adminhtml/system_config_source_allregion')->toOptionArray();
					break;
				case 'group_id':
					$options = Mage::getResourceModel('customer/group_collection')->load()->toOptionArray();
					break;
                }
			// Set the value_select_options           
            $this->setData('value_select_options', $options);
        }
		// Return the value_select_options           
        return $this->getData('value_select_options');
    }

    /**
     * Validate Customer Address
     *
     * @return bool
     */
    public function validate(Varien_Object $object)
    {
		// Get customer id
		$customerId = $object->getQuote()->getCustomerId();
		// Check if an Id is available
        if ($customerId){
			// Load the Customer
            $customer = Mage::getModel('customer/customer')->load($customerId);
			// Set the data
            $datas = $customer->getData();
			// return the Abstract validation
            return parent::validate($datas);
        }
        // Otherwise return false
        return false;
    }
}