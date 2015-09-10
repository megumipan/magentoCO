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

class IrvineSystems_Yamato_Block_Adminhtml_Slips_Edit_Tab_Receiving extends Mage_Adminhtml_Block_Widget_Form
{
   /**
    * Prepare Order Information Tab Form
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

		// Initialize Main Fieldsets
        $fieldset = $form->addFieldset('base_fieldset', array('legend'=>$helper->__('Receiving Agent Information')));

       // Add Enable to Use Receiving Agent Field
    	$fieldset->addField('rec_agent_flag', 'text', array(
			'name'				=> 'rec_agent_flag',
			'label'				=> $helper->__('Enable to Use Receiving Agent'),
            'class'				=> 'validate-length maximum-length-1',
			'after_element_html'=> '<br /><small>'.$helper->__('Update Enable to Use Receiving Agent').'</small>',
        ));

       // Add Receiving Agent QR code Field
    	$fieldset->addField('rec_agent_qr_code', 'text', array(
			'name'				=> 'rec_agent_qr_code',
			'label'				=> $helper->__('Receiving Agent QR code'),
            'class'				=> 'validate-length maximum-length-1',
			'after_element_html'=> '<br /><small>'.$helper->__('Update Receiving Agent QR code').'</small>',
        ));

       // Add Receiving Agent Amount(including tax) Field
    	$fieldset->addField('rec_agent_amount', 'text', array(
			'name'				=> 'rec_agent_amount',
			'label'				=> $helper->__('Receiving Agent Amount(including tax)'),
			'required'			=> true,
            'class'				=> 'validate-length maximum-length-7',
			'after_element_html'=> '<br /><small>'.$helper->__('Update Receiving Agent Amount(including tax)').'</small>',
        ));

       // Add Receiving Agent Amount of Tax Field
    	$fieldset->addField('rec_agent_amount_of_tax', 'text', array(
			'name'				=> 'rec_agent_amount_of_tax',
			'label'				=> $helper->__('Receiving Agent Amount of Tax'),
			'required'			=> true,
            'class'				=> 'validate-length maximum-length-7',
			'after_element_html'=> '<br /><small>'.$helper->__('Update Receiving Agent Amount of Tax').'</small>',
        ));

       // Add Receiving Agent Invoice PostCode Field
    	$fieldset->addField('rec_agent_invoice_postcode', 'text', array(
			'name'				=> 'rec_agent_invoice_postcode',
			'label'				=> $helper->__('Receiving Agent Invoice PostCode'),
			'required'			=> true,
            'class'				=> 'validate-length maximum-length-8',
			'after_element_html'=> '<br /><small>'.$helper->__('Update Receiving Agent Invoice PostCode').'</small>',
        ));

       // Add Receiving Agent Invoice Address Field
    	$fieldset->addField('rec_agent_invoice_address', 'text', array(
			'name'				=> 'rec_agent_invoice_address',
			'label'				=> $helper->__('Receiving Agent Invoice Address'),
			'required'			=> true,
            'class'				=> 'validate-length maximum-length-64',
			'after_element_html'=> '<br /><small>'.$helper->__('Update Receiving Agent Invoice Address').'</small>',
        ));

       // Add Receiving Agent Invoice Apartment Name Field
    	$fieldset->addField('rec_agent_invoice_appat_name', 'text', array(
			'name'				=> 'rec_agent_invoice_appat_name',
			'label'				=> $helper->__('Receiving Agent Invoice Apartment Name'),
            'class'				=> 'validate-length maximum-length-32',
			'after_element_html'=> '<br /><small>'.$helper->__('Update Receiving Agent Invoice Apartment Name').'</small>',
        ));

       // Add Department 1 of Receiving Agent Field
    	$fieldset->addField('rec_agent_department1', 'text', array(
			'name'				=> 'rec_agent_department1',
			'label'				=> $helper->__('Department 1 of Receiving Agent'),
            'class'				=> 'validate-length maximum-length-50',
			'after_element_html'=> '<br /><small>'.$helper->__('Update Department 1 of Receiving Agent').'</small>',
        ));

       // Add Department 2 of Receiving Agent Field
    	$fieldset->addField('rec_agent_department2', 'text', array(
			'name'				=> 'rec_agent_department2',
			'label'				=> $helper->__('Department 2 of Receiving Agent'),
            'class'				=> 'validate-length maximum-length-50',
			'after_element_html'=> '<br /><small>'.$helper->__('Update Department 2 of Receiving Agent').'</small>',
        ));

       // Add Receiving Agent Invoice Name Field
    	$fieldset->addField('rec_agent_invoice_name', 'text', array(
			'name'				=> 'rec_agent_invoice_name',
			'label'				=> $helper->__('Receiving Agent Invoice Name'),
			'required'			=> true,
            'class'				=> 'validate-length maximum-length-32',
			'after_element_html'=> '<br /><small>'.$helper->__('Update Receiving Agent Invoice Name').'</small>',
        ));

       // Add Receiving Agent Invoice Name (kana) Field
    	$fieldset->addField('rec_agent_invoice_name_kana', 'text', array(
			'name'				=> 'rec_agent_invoice_name_kana',
			'label'				=> $helper->__('Receiving Agent Invoice Name (kana)'),
			'required'			=> true,
            'class'				=> 'validate-length maximum-length-50',
			'after_element_html'=> '<br /><small>'.$helper->__('Update Receiving Agent Invoice Name (kana)').'</small>',
        ));

       // Add Receiving Agent Reference Name Field
    	$fieldset->addField('rec_agent_ref_name', 'text', array(
			'name'				=> 'rec_agent_ref_name',
			'label'				=> $helper->__('Receiving Agent Reference Name'),
			'required'			=> true,
            'class'				=> 'validate-length maximum-length-32',
			'after_element_html'=> '<br /><small>'.$helper->__('Update Receiving Agent Reference Name').'</small>',
        ));

       // Add Receiving Agent Reference PostCode Field
    	$fieldset->addField('rec_agent_ref_postcode', 'text', array(
			'name'				=> 'rec_agent_ref_postcode',
			'label'				=> $helper->__('Receiving Agent Reference PostCode'),
			'required'			=> true,
            'class'				=> 'validate-length maximum-length-8',
			'after_element_html'=> '<br /><small>'.$helper->__('Update Receiving Agent Reference PostCode').'</small>',
        ));

       // Add Receiving Agent Reference Address Field
    	$fieldset->addField('rec_agent_ref_address', 'text', array(
			'name'				=> 'rec_agent_ref_address',
			'label'				=> $helper->__('Receiving Agent Reference Address'),
			'required'			=> true,
            'class'				=> 'validate-length maximum-length-64',
			'after_element_html'=> '<br /><small>'.$helper->__('Update Receiving Agent Reference Address').'</small>',
        ));

       // Add Receiving Agent Reference Apartment Name Field
    	$fieldset->addField('rec_agent_ref_apart_name', 'text', array(
			'name'				=> 'rec_agent_ref_apart_name',
			'label'				=> $helper->__('Receiving Agent Reference Apartment Name'),
            'class'				=> 'validate-length maximum-length-32',
			'after_element_html'=> '<br /><small>'.$helper->__('Update Receiving Agent Reference Apartment Name').'</small>',
        ));

       // Add Receiving Agent Reference Telephone Number Field
    	$fieldset->addField('rec_agent_tel_num', 'text', array(
			'name'				=> 'rec_agent_tel_num',
			'label'				=> $helper->__('Receiving Agent Reference Telephone Number'),
			'required'			=> true,
            'class'				=> 'validate-length maximum-length-15',
			'after_element_html'=> '<br /><small>'.$helper->__('Update Receiving Agent Reference Telephone Number').'</small>',
        ));

       // Add Receiving Agent Number Field
    	$fieldset->addField('rec_agent_number', 'text', array(
			'name'				=> 'rec_agent_number',
			'label'				=> $helper->__('Receiving Agent Number'),
            'class'				=> 'validate-length maximum-length-20',
			'after_element_html'=> '<br /><small>'.$helper->__('Update Receiving Agent Number').'</small>',
        ));

       // Add Receiving Agent Product Name Field
    	$fieldset->addField('rec_agent_product_name', 'text', array(
			'name'				=> 'rec_agent_product_name',
			'label'				=> $helper->__('Receiving Agent Product Name'),
            'class'				=> 'validate-length maximum-length-50',
			'after_element_html'=> '<br /><small>'.$helper->__('Update Receiving Agent Product Name').'</small>',
        ));

       // Add Receiving Agent Comment Field
    	$fieldset->addField('rec_agent_comment', 'text', array(
			'name'				=> 'rec_agent_comment',
			'label'				=> $helper->__('Receiving Agent Comment'),
            'class'				=> 'validate-length maximum-length-28',
			'after_element_html'=> '<br /><small>'.$helper->__('Update Receiving Agent Comment').'</small>',
        ));

		// Finalize the Form
        $form->setValues($registry->getData());
        $this->setForm($form);
        return parent::_prepareForm();
    }
}
