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

class IrvineSystems_Yamato_Block_Adminhtml_Slips_Edit_Tab_Store extends Mage_Adminhtml_Block_Widget_Form
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
        $model = Mage::getModel('yamato/slips');
        // Set Helper Model
        $helper = Mage::helper('yamato');

        // Initialize Form
        $form = new Varien_Data_Form();
        // assifgn a prefix to the subform
        $form->setHtmlIdPrefix('slip_');

		// Initialize Actions Fieldsets
        $fieldset = $form->addFieldset('base_fieldset', array('legend'=>$helper->__('Store Information')));

        // Add Store Member Number Field
        $fieldset->addField('store_member_num', 'text', array(
            'name'				=> 'store_member_num',
            'label'				=> $helper->__('Store Member Number'),
            'class'				=> 'validate-length maximum-length-20',
			'after_element_html'=> '<br /><small>'.$helper->__('Update Store Member Number').'</small>',
        ));

        // Add Store Telephone Field
        $fieldset->addField('store_tel', 'text', array(
            'name'				=> 'store_tel',
            'label'				=> $helper->__('Store Telephone'),
			'required'			=> true,
            'class'				=> 'validate-length maximum-length-15',
			'after_element_html'=> '<br /><small>'.$helper->__('Update Store Telephone').'</small>',
        ));

        // Add Store Telephone Branch Number Field
        $fieldset->addField('store_tel_branch_num', 'text', array(
            'name'				=> 'store_tel_branch_num',
            'label'				=> $helper->__('Store Telephone Branch Number'),
            'class'				=> 'validate-length maximum-length-2',
			'after_element_html'=> '<br /><small>'.$helper->__('Update Store Telephone Branch Number').'</small>',
        ));

        // Add Store PostCode Field
        $fieldset->addField('store_postcode', 'text', array(
            'name'				=> 'store_postcode',
            'label'				=> $helper->__('Store Post Code'),
			'required'			=> true,
            'class'				=> 'validate-length maximum-length-8',
			'after_element_html'=> '<br /><small>'.$helper->__('Update Store Post Code').'</small>',
        ));

        // Add Store Address Field
        $fieldset->addField('store_address', 'text', array(
			'name'				=> 'store_address',
			'label'				=> $helper->__('Store Address'),
			'required'			=> true,
            'class'				=> 'validate-length maximum-length-64',
			'after_element_html'=> '<br /><small>'.$helper->__('Update Store Address').'</small>',
        ));

        // Add Store Apartment Name Field
    	$fieldset->addField('store_apart_name', 'text', array(
       		'name'				=> 'store_prefix',
			'label'				=> $helper->__('Store Apartment Name'),
            'class'				=> 'validate-length maximum-length-32',
			'after_element_html'=> '<br /><small>'.$helper->__('Update Store Apartment Name').'</small>',
        ));

        // Add Store Name Field
        $fieldset->addField('store_name', 'text', array(
            'name'				=> 'store_name',
            'label'				=> $helper->__('Store Name'),
			'required'			=> true,
            'class'				=> 'validate-length maximum-length-32',
			'after_element_html'=> '<br /><small>'.$helper->__('Update Store Name').'</small>',
        ));

        // Add Store Name (Kana) Field
        $fieldset->addField('store_name_kana', 'text', array(
            'name'				=> 'store_name_kana',
            'label'				=> $helper->__('Store Name (Kana)'),
            'class'				=> 'validate-length maximum-length-100',
			'after_element_html'=> '<br /><small>'.$helper->__('Update Store Name (Kana)').'</small>',
        ));
        
        // Add Invoice Customer ID Field
        $fieldset->addField('invoice_customer_id', 'text', array(
        		'name'				=> 'invoice_customer_id',
        		'label'				=> $helper->__('Invoice Customer ID'),
        		'title'				=> $helper->__('Invoice Customer ID'),
        		'required'			=> true,
        		'class'				=> 'validate-length maximum-length-15',
        		'after_element_html'=> '<br /><small>'.$helper->__('Update Invoice Customer ID').'</small>',
        ));
         
        // Add Invoice Class ID Field
        $fieldset->addField('invoice_class_id', 'text', array(
        		'name'				=> 'invoice_class_id',
        		'label'				=> $helper->__('Invoice Class ID'),
        		'title'				=> $helper->__('Invoice Class ID'),
        		'required'			=> true,
        		'class'				=> 'validate-length maximum-length-3',
        		'after_element_html'=> '<br /><small>'.$helper->__('Update Invoice Class ID').'</small>',
        ));
        
        // Add Shipping Charge Number Field
        $fieldset->addField('shipping_charge_number', 'text', array(
        		'name'				=> 'shipping_charge_number',
        		'label'				=> $helper->__('Shipping Charge Number'),
        		'title'				=> $helper->__('Shipping Charge Number'),
        		'required'			=> true,
        		'class'				=> 'validate-length maximum-length-2',
        		'after_element_html'=> '<br /><small>'.$helper->__('Update Shipping Charge Number').'</small>',
        ));
        
		// Finalize the Form
        $form->setValues($registry->getData());
        $this->setForm($form);
        return parent::_prepareForm();
    }
}