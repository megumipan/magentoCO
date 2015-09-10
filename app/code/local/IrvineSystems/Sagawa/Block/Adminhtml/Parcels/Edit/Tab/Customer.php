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

class IrvineSystems_Sagawa_Block_Adminhtml_Parcels_Edit_Tab_Customer extends Mage_Adminhtml_Block_Widget_Form
{
   /**
    * Prepare Customer Information Tab Form
    *
    * @return Mage_Adminhtml_Block_Widget_Form
    */
    protected function _prepareForm()
    {
        // Set Registry Model
        $registry = Mage::registry('slip_data');
        // Set Data Model
        $model = Mage::getModel('sagawa/slips');
        // Set Helper Model
        $helper = Mage::helper('sagawa');

		// Initialize Form
        $form = new Varien_Data_Form();
        // assifgn a prefix to the subform
        $form->setHtmlIdPrefix('slip_');

		// Initialize Main Fieldsets
        $fieldset = $form->addFieldset('base_fieldset', array('legend'=>$helper->__('Customer Information')));

        // Add Customer Address Book Code Field
        $fieldset->addField('customer_address_code', 'text', array(
            'name'				=> 'customer_address_code',
            'label'				=> $helper->__('Customer Address Book Code'),
            'class'				=> 'validate-length maximum-length-12',
			'after_element_html'=> '<br /><small>'.$helper->__('Update Customer Address Book Code (Max Length 12 Chars)').'</small>',
        ));

        // Add Customer Address Book Code Field
        $fieldset->addField('customer_memberid', 'text', array(
            'name'				=> 'customer_memberid',
            'label'				=> $helper->__('Customer Member ID'),
            'class'				=> 'validate-length maximum-length-16',
			'after_element_html'=> '<br /><small>'.$helper->__('Update Customer Member ID (Max Length 16 Chars)').'</small>',
        ));

        // Add Customer Full Name Field
        $fieldset->addField('customer_name', 'text', array(
            'name'				=> 'customer_name',
            'label'				=> $helper->__('Customer Full Name'),
            'class'				=> 'validate-length maximum-length-16',
			'required'			=> true,
			'after_element_html'=> '<br /><small>'.$helper->__('Update Customer Full Name').'</small>',
        ));

        // Add Customer Full Name (kana)' Field
        $fieldset->addField('customer_namekana', 'text', array(
            'name'				=> 'customer_namekana',
            'label'				=> $helper->__('Customer Full Name (kana)'),
            'class'				=> 'validate-length maximum-length-16',
			'after_element_html'=> '<br /><small>'.$helper->__('Update Customer Full Name (kana)').'</small>',
        ));

        // Add Customer Address (1Ln) Field
        $fieldset->addField('customer_address_1', 'text', array(
            'name'				=> 'customer_address_1',
            'label'				=> $helper->__('Customer Address (1)'),
            'class'				=> 'validate-length maximum-length-16',
			'required'			=> true,
			'after_element_html'=> '<br /><small>'.$helper->__('Update Customer Address (Max Length 16 Chars)').'</small>',
        ));

        // Add Customer Address (2Ln) Field
        $fieldset->addField('customer_address_2', 'text', array(
            'name'				=> 'customer_address_2',
            'label'				=> $helper->__('Customer Address (2)'),
            'class'				=> 'validate-length maximum-length-16',
			'after_element_html'=> '<br /><small>'.$helper->__('Update Customer Address (Max Length 16 Chars)').'</small>',
        ));

        // Add Customer Address (3Ln) Field
        $fieldset->addField('customer_address_3', 'text', array(
            'name'				=> 'customer_address_3',
            'label'				=> $helper->__('Customer Address (3)'),
            'class'				=> 'validate-length maximum-length-16',
			'after_element_html'=> '<br /><small>'.$helper->__('Update Customer Address (Max Length 16 Chars)').'</small>',
        ));

        // Add Customer Post Code Field
        $fieldset->addField('customer_postcode', 'text', array(
            'name'				=> 'customer_postcode',
            'label'				=> $helper->__('Customer Post Code'),
			'after_element_html'=> '<br /><small>'.$helper->__('Update Customer Post Code').'</small>',
        ));

        // Add Customer Telephone Field
        $fieldset->addField('customer_tel', 'text', array(
            'name'				=> 'customer_tel',
            'label'				=> $helper->__('Customer Telephone'),
			'after_element_html'=> '<br /><small>'.$helper->__('Update Customer Telephone').'</small>',
        ));

		// Finalize the Form
        $form->setValues($registry->getData());
        $this->setForm($form);
        return parent::_prepareForm();
    }
}