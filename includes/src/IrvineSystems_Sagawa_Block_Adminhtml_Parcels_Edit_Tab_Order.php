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

class IrvineSystems_Sagawa_Block_Adminhtml_Parcels_Edit_Tab_Order extends Mage_Adminhtml_Block_Widget_Form
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
        $model = Mage::getModel('sagawa/slips');
        // Set Helper Model
        $helper = Mage::helper('sagawa');

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
            'required'			=> true,
        ));

        // Add Tracking Number Field
    	$fieldset->addField('tracking_number', 'text', array(
            'name'				=> 'tracking_number',
            'label'				=> $helper->__('Tracking Number'),
            'title'				=> $helper->__('Tracking Number'),
			'after_element_html'=> '<br /><small>'.$helper->__('Update Tracking Number').'</small>',
        ));

        // Add Delivery Payment Source Field
    	$fieldset->addField('payment_source', 'select', array(
            'label'				=> $helper->__('Delivery Payment Source'),
            'title'				=> $helper->__('Delivery Payment Source'),
			'after_element_html'=> '<br /><small>'.$helper->__('Update Delivery Payment Source').'</small>',
            'name'				=> 'payment_source',
            'values'			=> $model->getDelPaySourceTypes(),
        ));

        // Add Cash on Delivery Amount Field
    	$fieldset->addField('cod_amount', 'text', array(
            'name'				=> 'cod_amount',
            'label'				=> $helper->__('Cash on Delivery Amount'),
            'title'				=> $helper->__('Cash on Delivery Amount'),
			'after_element_html'=> '<br /><small>'.$helper->__('Update Cash on Delivery Amount').'</small>',
        ));

        // Add Cash on Delivery Payment Method Field
    	$fieldset->addField('cod_method', 'select', array(
            'label'				=> $helper->__('Cash on Delivery Payment Method'),
            'title'				=> $helper->__('Cash on Delivery Payment Method'),
			'after_element_html'=> '<br /><small>'.$helper->__('Update Cash on Delivery Payment Method').'</small>',
            'name'				=> 'cod_method',
            'values'			=> $model->getCodPaymentMethodTypes(),
        ));

        // Add Packing Type Field
    	$fieldset->addField('packing_code', 'select', array(
            'label'				=> $helper->__('Packing Type'),
            'title'				=> $helper->__('Packing Type'),
			'after_element_html'=> '<br /><small>'.$helper->__('Update Packing Type').'</small>',
            'name'				=> 'packing_code',
            'values'			=> $model->getPkgCodeTypes(),
        ));

        // Add 1st Product Name Field
    	$fieldset->addField('product_name_1', 'text', array(
            'name'				=> 'product_name_1',
            'label'				=> $helper->__('1st Product Name'),
            'class'				=> 'validate-length maximum-length-32',
            'title'				=> $helper->__('1st Product Name'),
			'after_element_html'=> '<br /><small>'.$helper->__('Update 1st Product Name').'</small>',
        ));

        // Add 2nd Product Name Field
    	$fieldset->addField('product_name_2', 'text', array(
            'name'				=> 'product_name_2',
            'label'				=> $helper->__('2nd Product Name'),
            'class'				=> 'validate-length maximum-length-32',
            'title'				=> $helper->__('2nd Product Name'),
			'after_element_html'=> '<br /><small>'.$helper->__('Update 2nd Product Name').'</small>',
        ));

        // Add 3rd Product Name Field
    	$fieldset->addField('product_name_3', 'text', array(
            'name'				=> 'product_name_3',
            'label'				=> $helper->__('3rd Product Name'),
            'class'				=> 'validate-length maximum-length-32',
            'title'				=> $helper->__('3rd Product Name'),
			'after_element_html'=> '<br /><small>'.$helper->__('Update 3rd Product Name').'</small>',
        ));

        // Add 4th Product Name Field
    	$fieldset->addField('product_name_4', 'text', array(
            'name'				=> 'product_name_4',
            'label'				=> $helper->__('4th Product Name'),
            'class'				=> 'validate-length maximum-length-32',
            'title'				=> $helper->__('4th Product Name'),
			'after_element_html'=> '<br /><small>'.$helper->__('Update 4th Product Name').'</small>',
        ));

        // Add 5th Product Name Field
    	$fieldset->addField('product_name_5', 'text', array(
            'name'				=> 'product_name_5',
            'label'				=> $helper->__('5th Product Name'),
            'class'				=> 'validate-length maximum-length-32',
            'title'				=> $helper->__('5th Product Name'),
			'after_element_html'=> '<br /><small>'.$helper->__('Update 5th Product Name').'</small>',
        ));

        // Add Total Number Of Packages Field
    	$fieldset->addField('packages_number', 'text', array(
            'name'				=> 'packages_number',
            'label'				=> $helper->__('Total Number Of Packages'),
            'class'				=> 'validate-greater-than-zero validate-length maximum-length-3',
            'title'				=> $helper->__('Total Number Of Packages'),
			'after_element_html'=> '<br /><small>'.$helper->__('Update Total Number Of Packages').'</small>',
        ));

        // Add Tax Amount Field
    	$fieldset->addField('tax_amount', 'text', array(
            'name'				=> 'tax_amount',
            'label'				=> $helper->__('Tax Amount'),
            'class'				=> 'validate-length maximum-length-6',
            'title'				=> $helper->__('Tax Amount'),
			'after_element_html'=> '<br /><small>'.$helper->__('Update Tax Amount').'</small>',
        ));

        // Add Ensured Amount Field
    	$fieldset->addField('ensured_amount', 'text', array(
            'name'				=> 'ensured_amount',
            'label'				=> $helper->__('Ensured Amount'),
            'class'				=> 'validate-length maximum-length-8',
            'title'				=> $helper->__('Ensured Amount'),
			'after_element_html'=> '<br /><small>'.$helper->__('Update Ensured Amount. (From 0 to 49999999)').'</small>',
        ));

        // Add Ensured Amount Printing Field
    	$fieldset->addField('ensured_amount_printed', 'select', array(
            'label'				=> $helper->__('Ensured Amount Printing'),
            'title'				=> $helper->__('Ensured Amount Printing'),
			'after_element_html'=> '<br /><small>'.$helper->__('Update Ensured Amount Printing').'</small>',
            'name'				=> 'ensured_amount_printed',
            'values'			=> $model->getEnsuredPrintTypes(),
        ));

		// Finalize the Form
        $form->setValues($registry->getData());
        $this->setForm($form);
        return parent::_prepareForm();
    }
}