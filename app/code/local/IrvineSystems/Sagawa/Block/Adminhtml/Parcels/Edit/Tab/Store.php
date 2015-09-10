<?php
/*
 * Irvine Systems Shipping Japan Sgw
 *
 * NOTICE OF LICENSE
 * This source file is subject to the Revised BSD License which is bundled with
 * this package in the files LICENSE.txt and LICENSE.html.
 *
 * @category	Magento Shipping Extension
 * @package		IrvineSystems_Sagawa
 * @author Irvine Systems, Inc.
 * @copyright Copyright (c) 2015, Irvine Systems, Inc. (http://irvinesystems.co.jp/)
 * @license     Revised BSD License
 */

class IrvineSystems_Sagawa_Block_Adminhtml_Parcels_Edit_Tab_Store extends Mage_Adminhtml_Block_Widget_Form
{
   /**
    * Prepare Customer Options Tab Form
    *
    * @return Mage_Adminhtml_Block_Widget_Form
    */
    protected function _prepareForm()
    {
        // Set Data Registry
        $registry = Mage::registry('slip_data');
        // Set Data Model
        $model = Mage::getModel('sagawa/slips');
        // Set Helper Model
        $helper = Mage::helper('sagawa');

        // Initialize Form
        $form = new Varien_Data_Form();
        // assifgn a prefix to the subform
        $form->setHtmlIdPrefix('slip_');

		// Initialize Actions Fieldsets
        $fieldset = $form->addFieldset('base_fieldset', array('legend'=>$helper->__('Store Information')));

        // Add Store ID Field
        $fieldset->addField('customer_id', 'text', array(
            'name'				=> 'customer_id',
            'label'				=> $helper->__('Store ID'),
            'class'				=> 'validate-digits validate-length maximum-length-12',
			'required'			=> true,
			'after_element_html'=> '<br /><small>'.$helper->__('Update Store ID').'</small>',
        ));

        // Add Store Name Field
        $fieldset->addField('store_name', 'text', array(
            'name'				=> 'store_name',
            'label'				=> $helper->__('Store Name'),
            'class'				=> 'validate-length maximum-length-16',
			'after_element_html'=> '<br /><small>'.$helper->__('Update Store Name').'</small>',
        ));

        // Add Store Name (Kana) Field
        $fieldset->addField('store_namekana', 'text', array(
            'name'				=> 'store_namekana',
            'label'				=> $helper->__('Store Name (Kana)'),
            'class'				=> 'validate-length maximum-length-16',
			'after_element_html'=> '<br /><small>'.$helper->__('Update Store Name (Kana)').'</small>',
        ));

        // Add Store Address (1Ln) Field
        $fieldset->addField('store_address_1', 'text', array(
            'name'				=> 'store_address_1',
            'label'				=> $helper->__('Store Address (1st Line)'),
            'class'				=> 'validate-length maximum-length-16',
			'after_element_html'=> '<br /><small>'.$helper->__('Update Store Address (Max Length 16 Chars)').'</small>',
        ));

        // Add Store Address (2Ln) Field
        $fieldset->addField('store_address_2', 'text', array(
            'name'				=> 'store_address_2',
            'label'				=> $helper->__('Store Address (2nd Line)'),
            'class'				=> 'validate-length maximum-length-16',
			'after_element_html'=> '<br /><small>'.$helper->__('Update Store Address (Max Length 16 Chars)').'</small>',
        ));

        // Add Store Postcode Field
        $fieldset->addField('store_postcode', 'text', array(
            'name'				=> 'store_postcode',
            'label'				=> $helper->__('Store Postcode'),
			'after_element_html'=> '<br /><small>'.$helper->__('Update Store Postcode').'</small>',
        ));

        // Add Contact Name Field
        $fieldset->addField('store_contact', 'text', array(
            'name'				=> 'store_contact',
            'label'				=> $helper->__('Contact or Person in Charge Name'),
            'class'				=> 'validate-length maximum-length-16',
			'after_element_html'=> '<br /><small>'.$helper->__('Update Contact or Person in Charge Name').'</small>',
        ));

        // Add Store Telephone Field
        $fieldset->addField('store_tel', 'text', array(
            'name'				=> 'store_tel',
            'label'				=> $helper->__('Store Telephone'),
			'after_element_html'=> '<br /><small>'.$helper->__('Update Store Telephone').'</small>',
        ));

        // Add Store Telephone Field
        $fieldset->addField('shipper_tel', 'text', array(
            'name'				=> 'shipper_tel',
            'label'				=> $helper->__('Shipper Telephone'),
			'after_element_html'=> '<br /><small>'.$helper->__('Update Shipper Telephone').'</small>',
        ));

		// Finalize the Form
        $form->setValues($registry->getData());
        $this->setForm($form);
        return parent::_prepareForm();
    }
}