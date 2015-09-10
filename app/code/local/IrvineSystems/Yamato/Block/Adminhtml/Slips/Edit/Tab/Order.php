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

class IrvineSystems_Yamato_Block_Adminhtml_Slips_Edit_Tab_Order extends Mage_Adminhtml_Block_Widget_Form
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
        $fieldset = $form->addFieldset('base_fieldset', array('legend'=>$helper->__('Order Information')));

        // Check if we are editing a Rule or create a new one in order to show or not the Rule ID
        if ($registry->getId()) {
	        // Add Slip ID Field
        	$fieldset->addField('slip_id', 'hidden', array(
                'name'				=> 'slip_id',
            ));
        }
		
       // Add Order Number Field
    	$fieldset->addField('order_id', 'text', array(
            'name'				=> 'order_id',
            'label'				=> $helper->__('Order Number'),
            'title'				=> $helper->__('Order Number'),
			'after_element_html'=> '<br /><small>'.$helper->__('Update Order Number').'</small>',
        ));

       // Add Tracking Number Field
    	$fieldset->addField('tracking_number', 'text', array(
            'name'				=> 'tracking_number',
            'label'				=> $helper->__('Tracking Number'),
            'title'				=> $helper->__('Tracking Number'),
			'after_element_html'=> '<br /><small>'.$helper->__('Update Tracking Number').'</small>',
        ));

       // Add Card Payment Entry Field
    	$fieldset->addField('card_pay_entry', 'text', array(
            'name'				=> 'card_pay_entry',
            'label'				=> $helper->__('Card Payment Entry'),
            'title'				=> $helper->__('Card Payment Entry'),
            'class'				=> 'validate-length maximum-length-1',
			'after_element_html'=> '<br /><small>'.$helper->__('Update Card Payment Entry').'</small>',
        ));

       // Add Card Payment Shop Number Field
    	$fieldset->addField('card_pay_shop_number', 'text', array(
            'name'				=> 'card_pay_shop_number',
            'label'				=> $helper->__('Card Payment Shop Number'),
            'title'				=> $helper->__('Card Payment Shop Number'),
			'required'			=> true,
            'class'				=> 'validate-length maximum-length-9',
			'after_element_html'=> '<br /><small>'.$helper->__('Update Card Payment Shop Number').'</small>',
        ));

       // Add Card Payment Acceptance Number 1 Field
    	$fieldset->addField('card_pay_acceptance_number1', 'text', array(
            'name'				=> 'card_pay_acceptance_number1',
            'label'				=> $helper->__('Card Payment Acceptance Number 1'),
            'title'				=> $helper->__('Card Payment Acceptance Number 1'),
			'required'			=> true,
            'class'				=> 'validate-length maximum-length-23',
			'after_element_html'=> '<br /><small>'.$helper->__('Update Card Payment Acceptance Number 1').'</small>',
        ));

       // Add Card Payment Acceptance Number 2 Field
    	$fieldset->addField('card_pay_acceptance_number2', 'text', array(
            'name'				=> 'card_pay_acceptance_number2',
            'label'				=> $helper->__('Card Payment Acceptance Number 2'),
            'title'				=> $helper->__('Card Payment Acceptance Number 2'),
            'class'				=> 'validate-length maximum-length-23',
			'after_element_html'=> '<br /><small>'.$helper->__('Update Card Payment Acceptance Number 2').'</small>',
        ));

    	// Add Card Payment Acceptance Number 3 Field
    	$fieldset->addField('card_pay_acceptance_number3', 'text', array(
            'name'				=> 'card_pay_acceptance_number3',
            'label'				=> $helper->__('Card Payment Acceptance Number 3'),
            'title'				=> $helper->__('Card Payment Acceptance Number 3'),
            'class'				=> 'validate-length maximum-length-23',
			'after_element_html'=> '<br /><small>'.$helper->__('Update Card Payment Acceptance Number 3').'</small>',
        ));

        // Add Enable Email to Notice Schedule Field
        $fieldset->addField('enable_email_notice_schedule', 'select', array(
            'name'				=> 'enable_email_notice_schedule',
            'label'				=> $helper->__('Enable Email to Notice Schedule'),
			'after_element_html'=> '<br /><small>'.$helper->__('Update Enable Email to Notice Schedule').'</small>',
            'values'			=> $model->getBoolTypes(),
            'onchange'			=> 'checkNoticeSchedule()',
        ));

    	// Add Email to Notice Schedule Field
    	$fieldset->addField('email_notice_schedule', 'text', array(
            'name'				=> 'email_notice_schedule',
            'label'				=> $helper->__('Email to Notice Schedule'),
            'title'				=> $helper->__('Email to Notice Schedule'),
			'after_element_html'=> '<br /><small>'.$helper->__('Update Email to Notice Schedule').'</small>',
        ));

        // Add Enable Email to Notice Schedule Field
        $fieldset->addField('input_equipment', 'select', array(
            'name'				=> 'input_equipment',
            'label'				=> $helper->__('Input Equipment'),
			'after_element_html'=> '<br /><small>'.$helper->__('Update Input Equipment').'</small>',
            'values'			=> $model->getMailDeviceTypes(),
        ));

    	// Add Email Message to Notice Schedule Field
    	$fieldset->addField('email_message_notice_schedule', 'text', array(
            'name'				=> 'email_message_notice_schedule',
            'label'				=> $helper->__('Email Message to Notice Schedule'),
            'title'				=> $helper->__('Email Message to Notice Schedule'),
			'after_element_html'=> '<br /><small>'.$helper->__('Update Email Message to Notice Schedule').'</small>',
        ));

        // Add Enable Email to Notice Complete Field
        $fieldset->addField('enable_email_notice_complete', 'select', array(
            'name'				=> 'enable_email_notice_complete',
            'label'				=> $helper->__('Enable Email to Notice Complete'),
			'after_element_html'=> '<br /><small>'.$helper->__('Update Enable Email to Notice Complete').'</small>',
            'values'			=> $model->getBoolTypes(),
            'onchange'			=> 'checkNoticeComplete()',
        ));

    	// Add Email to Notice Complete Field
    	$fieldset->addField('email_notice_complete', 'text', array(
            'name'				=> 'email_notice_complete',
            'label'				=> $helper->__('Email to Notice Complete'),
            'title'				=> $helper->__('Email to Notice Complete'),
			'after_element_html'=> '<br /><small>'.$helper->__('Update Email to Notice Complete').'</small>',
        ));

    	// Add Email Message to Notice Complete Field
    	$fieldset->addField('email_message_notice_complete', 'text', array(
            'name'				=> 'email_message_notice_complete',
            'label'				=> $helper->__('Email Message to Notice Complete'),
            'title'				=> $helper->__('Email Message to Notice Complete'),
			'after_element_html'=> '<br /><small>'.$helper->__('Update Email Message to Notice Complete').'</small>',
        ));
    	
		// Finalize the Form
        $form->setValues($registry->getData());
        $this->setForm($form);
        return parent::_prepareForm();
    }
}
