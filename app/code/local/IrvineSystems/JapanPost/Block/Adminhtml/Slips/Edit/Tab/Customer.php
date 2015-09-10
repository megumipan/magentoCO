<?php
/*
 * Irvine Systems Shipping Japan Jp
 *
 * NOTICE OF LICENSE
 * This source file is subject to the Revised BSD License which is bundled with
 * this package in the files LICENSE.txt and LICENSE.html.
 *
 * @category	Magento Shipping Extension
 * @package		IrvineSystems_JapanPost
 * @author Irvine Systems, Inc.
 * @copyright Copyright (c) 2015, Irvine Systems, Inc. (http://irvinesystems.co.jp/)
 * @license     Revised BSD License
 */

class IrvineSystems_JapanPost_Block_Adminhtml_Slips_Edit_Tab_Customer extends Mage_Adminhtml_Block_Widget_Form
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
        $model = Mage::getModel('japanpost/slips');
        // Set Helper Model
        $helper = Mage::helper('japanpost');

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
            'class'				=> 'validate-digits validate-length maximum-length-10',
			'required'			=> true,
			'after_element_html'=> '<br /><small>'.$helper->__('Update Customer ID').'</small>',
        ));

        // Add Discount Type Field
    	$fieldset->addField('customer_prefix', 'select', array(
            'label'				=> $helper->__('Customer Prefix'),
            'title'				=> $helper->__('Customer Prefix'),
			'after_element_html'=> '<br /><small>'.$helper->__('Update Customer Prefix').'</small>',
            'name'				=> 'customer_prefix',
            'values'			=> $model->getPrefixTypes(),
        ));

        // Add Customer Full Name Field
        $fieldset->addField('customer_name', 'text', array(
            'name'				=> 'customer_name',
            'label'				=> $helper->__('Customer Full Name'),
			'required'			=> true,
			'after_element_html'=> '<br /><small>'.$helper->__('Update Customer Full Name').'</small>',
        ));

        // Add Customer Full Name (kana)' Field
        $fieldset->addField('customer_namekana', 'text', array(
            'name'				=> 'customer_namekana',
            'label'				=> $helper->__('Customer Full Name (kana)'),
			'after_element_html'=> '<br /><small>'.$helper->__('Update Customer Full Name (kana)').'</small>',
        ));

        // Add Customer Address Field
        $fieldset->addField('customer_address', 'textarea', array(
            'name'				=> 'customer_address',
            'label'				=> $helper->__('Customer Address'),
            'class'				=> 'validate-length maximum-length-120',
			'required'			=> true,
			'after_element_html'=> '<br /><small>'.$helper->__('Update Customer Address').'</small>',
        ));

        // Add Customer Post Code Field
        $fieldset->addField('customer_postcode', 'text', array(
            'name'				=> 'customer_postcode',
            'label'				=> $helper->__('Customer Post Code'),
			'required'			=> true,
			'after_element_html'=> '<br /><small>'.$helper->__('Update Customer Post Code').'</small>',
        ));

        // Add Customer Telephone Field
        $fieldset->addField('customer_tel', 'text', array(
            'name'				=> 'customer_tel',
            'label'				=> $helper->__('Customer Telephone'),
			'after_element_html'=> '<br /><small>'.$helper->__('Update Customer Telephone').'</small>',
        ));

        // Add Customer Email Field
        $fieldset->addField('customer_email', 'text', array(
            'name'				=> 'customer_email',
            'label'				=> $helper->__('Customer Email'),
            'class'				=> 'validate-email',
			'after_element_html'=> '<br /><small>'.$helper->__('Update Customer Email').'</small>',
        ));

        // Add Post Notification Field
    	$fieldset->addField('notification_post', 'select', array(
            'label'				=> $helper->__('Post Notification'),
            'title'				=> $helper->__('Post Notification'),
			'after_element_html'=> '<br /><small>'.$helper->__('Update Post Notification').'</small>',
            'name'				=> 'notification_post',
            'values'			=> $model->getBoolTypes(),
        ));

        // Add Email Notification Field
    	$fieldset->addField('notification_email', 'select', array(
            'label'				=> $helper->__('Email Notification'),
            'title'				=> $helper->__('Email Notification'),
			'after_element_html'=> '<br /><small>'.$helper->__('Update Email Notification').'</small>',
            'name'				=> 'notification_email',
            'values'			=> $model->getBoolTypes(),
        ));

		// Finalize the Form
        $form->setValues($registry->getData());
        $this->setForm($form);
        return parent::_prepareForm();
    }
}