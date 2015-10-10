<?php
/*
 * Irvine Systems Shipping Japan Ymt
 *
 * NOTICE OF LICENSE
 * This source file is subject to the Revised BSD License which is bundled with
 * this package in the files LICENSE.txt and LICENSE.html.
 *
 * @category	Magento Shipping Extension
 * @package		IrvineSystems_Yamato
 * @author Irvine Systems, Inc.
 * @copyright Copyright (c) 2015, Irvine Systems, Inc. (http://irvinesystems.co.jp/)
 * @license     Revised BSD License
 */

class IrvineSystems_Yamato_Block_Adminhtml_Slips_Edit_Tab_Customer extends Mage_Adminhtml_Block_Widget_Form
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
        $model = Mage::getModel('yamato/slips');
        // Set Helper Model
        $helper = Mage::helper('yamato');

		// Initialize Form
        $form = new Varien_Data_Form();
        // assifgn a prefix to the subform
        $form->setHtmlIdPrefix('slip_');

		// Initialize Main Fieldsets
        $fieldset = $form->addFieldset('base_fieldset', array('legend'=>$helper->__('Customer Information')));

        // Add Customer ID Field
        $fieldset->addField('customer_id', 'text', array(
            'name'				=> 'customer_id',
            'label'				=> $helper->__('Customer ID'),
            'class'				=> 'validate-digits validate-length maximum-length-20',
			'after_element_html'=> '<br /><small>'.$helper->__('Update Customer ID').'</small>',
        ));
        
        // Add Customer Number Field
        $fieldset->addField('customer_number', 'text', array(
			'name'				=> 'customer_number',
			'label'				=> $helper->__('Customer Number'),
			'class'				=> 'validate-digits validate-length maximum-length-20',
			'after_element_html'=> '<br /><small>'.$helper->__('Update Customer Number').'</small>',
        ));
        
        // Add Customer Telephone Field
        $fieldset->addField('customer_tel', 'text', array(
			'name'				=> 'customer_tel',
			'label'				=> $helper->__('Customer Telephone'),
			'required'			=> true,
            'class'				=> 'validate-length maximum-length-15',
			'after_element_html'=> '<br /><small>'.$helper->__('Update Customer Telephone').'</small>',
        ));
        
        // Add Customer Email Field
        $fieldset->addField('customer_tel_branch_num', 'text', array(
			'name'				=> 'customer_tel_branch_num',
			'label'				=> $helper->__('Customer Telephone Branch Number'),
            'class'				=> 'validate-digits validate-length maximum-length-2',
			'after_element_html'=> '<br /><small>'.$helper->__('Update Customer Telephone Branch Number').'</small>',
        ));
        
        // Add Customer Post Code Field
        $fieldset->addField('customer_postcode', 'text', array(
			'name'				=> 'customer_postcode',
			'label'				=> $helper->__('Customer Post Code'),
			'required'			=> true,
            'class'				=> 'validate-length maximum-length-64',
            'class'				=> 'validate-length maximum-length-8',
			'after_element_html'=> '<br /><small>'.$helper->__('Update Customer Post Code').'</small>',
        ));
        
        // Add Customer Address Field
        $fieldset->addField('customer_address', 'text', array(
			'name'				=> 'customer_address',
			'label'				=> $helper->__('Customer Address'),
			'required'			=> true,
            'class'				=> 'validate-length maximum-length-64',
			'after_element_html'=> '<br /><small>'.$helper->__('Update Customer Address').'</small>',
        ));
        
        // Add Post Notification Field
    	$fieldset->addField('customer_apart_name', 'text', array(
  			'name'				=> 'customer_apart_name',
			'label'				=> $helper->__('Customer Apartment Name'),
            'class'				=> 'validate-length maximum-length-32',
			'after_element_html'=> '<br /><small>'.$helper->__('Update Customer Apartment Name').'</small>',
        ));

        // Add Post Notification Field
    	$fieldset->addField('customer_department1', 'text', array(
       		'name'				=> 'customer_department1',
			'label'				=> $helper->__('Department 1 of Customer'),
            'class'				=> 'validate-length maximum-length-50',
			'after_element_html'=> '<br /><small>'.$helper->__('Update Department 1 of Customer').'</small>',
        ));

    	// Add Email Notification Field
    	$fieldset->addField('customer_department2', 'text', array(
			'name'				=> 'customer_department2',
			'label'				=> $helper->__('Department 2 of Customer'),
            'class'				=> 'validate-length maximum-length-50',
			'after_element_html'=> '<br /><small>'.$helper->__('Update Department 2 of Customer').'</small>',
        ));

        // Add Customer Full Name Field
        $fieldset->addField('customer_full_name', 'text', array(
			'name'				=> 'customer_full_name',
			'label'				=> $helper->__('Customer Full Name'),
			'required'			=> true,
            'class'				=> 'validate-length maximum-length-32',
			'after_element_html'=> '<br /><small>'.$helper->__('Update Customer Full Name').'</small>',
        ));

        // Add Customer Full Name (kana)' Field
        $fieldset->addField('customer_full_name_kana', 'text', array(
            'name'				=> 'customer_full_name_kana',
            'label'				=> $helper->__('Customer Full Name (kana)'),
            'class'				=> 'validate-length maximum-length-100',
			'after_element_html'=> '<br /><small>'.$helper->__('Update Customer Full Name (kana)').'</small>',
        ));

        // Add Discount Type Field
    	$fieldset->addField('customer_prefix', 'text', array(
       		'name'				=> 'customer_prefix',
			'label'				=> $helper->__('Customer Prefix'),
       		'title'				=> $helper->__('Customer Prefix'),
            'class'				=> 'validate-length maximum-length-4',
			'after_element_html'=> '<br /><small>'.$helper->__('Update Customer Prefix').'</small>',
        ));
    	
		// Finalize the Form
        $form->setValues($registry->getData());
        $this->setForm($form);
        return parent::_prepareForm();
    }
}