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

class IrvineSystems_Awardpoints_Block_Adminhtml_Catalogrules_Edit_Tab_Actions extends Mage_Adminhtml_Block_Widget_Form
{
   /**
    * Prepare Action Tab Form
    *
    * @return Mage_Adminhtml_Block_Widget_Form
    */
    protected function _prepareForm()
    {
        // Set Data Registry
        $registry = Mage::registry('catalogrules_data');
        // Set Data Model
        $model = Mage::getModel('awardpoints/awardpoints');

        // Initialize Form
        $form = new Varien_Data_Form();
        // assifgn a prefix to the subform
        $form->setHtmlIdPrefix('rule_');

		// Initialize Actions Fieldsets
        $fieldset = $form->addFieldset('action_fieldset', array('legend'=>Mage::helper('awardpoints')->__('Actions')));

        // Add Action Type Field
        $fieldset->addField('action_type', 'select', array(
            'label'				=> Mage::helper('awardpoints')->__('Type of Action'),
			'after_element_html'=> '<br /><small>'.Mage::helper('awardpoints')->__('Select the rule type of action').'</small>',
            'name'				=> 'action_type',
            'onchange'			=> 'checkTypes()',
            'values'			=> $model->ruleActionTypesToOptionArray(),
            'required'			=> true,
        ));

        // Add Points Field
		// Class Validation and visibility will be change by Javascript
		// @see IrvineSystems_Awardpoints_Block_Adminhtml_Cartrules_Edit (__construct)
        $fieldset->addField('points', 'text', array(
            'name'				=> 'points',
			'after_element_html'=> '<br /><small>'.Mage::helper('awardpoints')->__('Set the points to re added or removed (use - for remove points)').'</small>',
            'label'				=> Mage::helper('awardpoints')->__('Points'),
            'title'				=> Mage::helper('awardpoints')->__('Points'),
            'required'			=> true,
        ));

		// Finalize the Form
        $form->setValues($registry->getData());
        $this->setForm($form);
        return parent::_prepareForm();
    }
}