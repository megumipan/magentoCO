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

class IrvineSystems_Awardpoints_Block_Adminhtml_Cartrules_Edit_Tab_Main extends Mage_Adminhtml_Block_Widget_Form
{
   /**
    * Prepare Main Tab Form
    *
    * @return Mage_Adminhtml_Block_Widget_Form
    */
    protected function _prepareForm()
    {
        // Set Data Registry
        $registry = Mage::registry('cartrules_data');
        // Set Data Model
        $model = Mage::getModel('awardpoints/awardpoints');

        // Initialize Form
        $form = new Varien_Data_Form();
        // assifgn a prefix to the subform
        $form->setHtmlIdPrefix('rule_');

		// Initialize Main Fieldsets
        $fieldset = $form->addFieldset('base_fieldset', array('legend'=>Mage::helper('awardpoints')->__('General Information')));

        // Check if we are editing a Rule or create a new one in order to show or not the Rule ID
        if ($registry->getId()) {
	        // Add Rule ID Field
        	$fieldset->addField('rule_id', 'hidden', array(
				'name'				=> 'rule_id',
            ));
        }
		
        // Add Title Field
    	$fieldset->addField('title', 'text', array(
            'name'				=> 'title',
            'label'				=> Mage::helper('awardpoints')->__('Rule Name'),
            'title'				=> Mage::helper('awardpoints')->__('Rule Name'),
			'after_element_html'=> '<br /><small>'.Mage::helper('awardpoints')->__('Set a name for the rule').'</small>',
            'required'			=> true,
        ));

        // Add Status Field
    	$fieldset->addField('status', 'select', array(
            'label'				=> Mage::helper('awardpoints')->__('Rule Status'),
            'title'				=> Mage::helper('awardpoints')->__('Rule Status'),
			'after_element_html'=> '<br /><small>'.Mage::helper('awardpoints')->__('Select the rule status').'</small>',
            'name'				=> 'status',
            'values'			=> $model->rulesStatesToOptionArray(),
        ));

        // Check if we need a multi or single selector for the website ids
        if (!Mage::app()->isSingleStoreMode()) {
            $fieldset->addField('website_ids', 'multiselect', array(
                'name'				=> 'website_ids[]',
                'label'				=> Mage::helper('awardpoints')->__('Websites'),
                'title'				=> Mage::helper('awardpoints')->__('Websites'),
				'after_element_html'=> '<br /><small>'.Mage::helper('awardpoints')->__('Select the websites in which the rule will be available').'</small>',
                'required'			=> true,
                'values'			=> Mage::getSingleton('adminhtml/system_config_source_website')->toOptionArray(),
            ));
        }
        else {
            $fieldset->addField('website_ids', 'hidden', array(
                'name'			=> 'website_ids[]',
                'value'			=> Mage::app()->getStore(true)->getWebsiteId()
            ));
            $registry->setWebsiteIds(Mage::app()->getStore(true)->getWebsiteId());
        }

        // Prepare Customer Groups Ids multiselector
        $customerGroups = Mage::getResourceModel('customer/group_collection')->load()->toOptionArray();

		// Check if the "NOT LOGGED IN" group is still present
        $found = false;
        foreach ($customerGroups as $group) {
            if ($group['value']==0) {
                $found = true;
            }
        }
        // If not, add it
        if (!$found) {
            array_unshift($customerGroups, array('value'=>0, 'label'=>Mage::helper('awardpoints')->__('NOT LOGGED IN')));
        }

        // Add Customer Group Ids Field
        $fieldset->addField('customer_group_ids', 'multiselect', array(
            'name'				=> 'customer_group_ids[]',
            'label'				=> Mage::helper('awardpoints')->__('Customer Groups'),
            'title'				=> Mage::helper('awardpoints')->__('Customer Groups'),
			'after_element_html'=> '<br /><small>'.Mage::helper('awardpoints')->__('Select the customers group for which the rule will be available').'</small>',
            'required'			=> true,
            'values'			=> $customerGroups,
        ));

        // Set the Date data Format
        $dateFormatIso = Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT);
        // Add From Date Field
        $fieldset->addField('from_date', 'date', array(
            'name'				=> 'from_date',
            'label'				=> Mage::helper('awardpoints')->__('From Date'),
            'title'				=> Mage::helper('awardpoints')->__('From Date'),
			'after_element_html'=> '<br /><small>'.Mage::helper('awardpoints')->__('Date in which the rule will start to be available').'</small>',
            'image'				=> $this->getSkinUrl('images/grid-cal.gif'),
            'input_format'		=> Varien_Date::DATE_INTERNAL_FORMAT,
            'format'			=> $dateFormatIso
        ));

        // Add To Date Field
        $fieldset->addField('to_date', 'date', array(
            'name'				=> 'to_date',
            'label'				=> Mage::helper('awardpoints')->__('To Date'),
            'title'				=> Mage::helper('awardpoints')->__('To Date'),
			'after_element_html'=> '<br /><small>'.Mage::helper('awardpoints')->__('Date in which the rule will expire').'</small>',
            'image'				=> $this->getSkinUrl('images/grid-cal.gif'),
            'input_format'		=> Varien_Date::DATE_INTERNAL_FORMAT,
            'format'			=> $dateFormatIso
        ));

        // Add Rule Priority Field
        $fieldset->addField('sort_order', 'text', array(
            'name'				=> 'sort_order',
            'label'				=> Mage::helper('awardpoints')->__('Priority'),
			'after_element_html'=> '<br /><small>'.Mage::helper('awardpoints')->__('Set the rule priority').'</small>',
        ));

		// Finalize the Form
        $form->setValues($registry->getData());
        $this->setForm($form);
        return parent::_prepareForm();
    }
}