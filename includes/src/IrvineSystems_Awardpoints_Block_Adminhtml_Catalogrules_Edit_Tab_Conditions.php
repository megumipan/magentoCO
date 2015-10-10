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

class IrvineSystems_Awardpoints_Block_Adminhtml_Catalogrules_Edit_Tab_Conditions extends Mage_Adminhtml_Block_Widget_Form
{
   /**
    * Prepare Conditions Tab Form
    *
    * @return Mage_Adminhtml_Block_Widget_Form
    */
    protected function _prepareForm()
    {
        // Set Registry Model
        $registry = Mage::registry('catalogrules_data');
        
		// Initialize Form
        $form = new Varien_Data_Form();
        // assifgn a prefix to the subform
        $form->setHtmlIdPrefix('rule_');

        // Set widget renderer
        $renderer = Mage::getBlockSingleton('adminhtml/widget_form_renderer_fieldset')
            ->setTemplate('promo/fieldset.phtml')
            ->setNewChildUrl($this->getUrl('*/adminhtml_catalogrules/newConditionHtml/form/rule_conditions_fieldset'));

        // Set Conditions FieldSet
        $fieldset = $form->addFieldset('conditions_fieldset', array(
            'legend'	=>Mage::helper('awardpoints')->__('Conditions'))
        )->setRenderer($renderer);

        // Add Conditions Field
    	$fieldset->addField('conditions', 'text', array(
            'name'		=> 'conditions',
            'label'		=> Mage::helper('awardpoints')->__('Conditions'),
            'title'		=> Mage::helper('awardpoints')->__('Conditions'),
            'required'	=> true,
        ))->setRule($registry)->setRenderer(Mage::getBlockSingleton('rule/conditions'));

        // Add Note Field
    	$fieldset->addField('note', 'note', array(
          'text'		=> Mage::helper('awardpoints')->__('Set all conditions and logic for the rule validation'),
        ));

		// Finalize the Form
        $form->setValues($registry->getData());
        $this->setForm($form);
        return parent::_prepareForm();
    }
}