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

class IrvineSystems_JapanPost_Block_Adminhtml_Slips_Edit_Tab_Order extends Mage_Adminhtml_Block_Widget_Form
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
        $model = Mage::getModel('japanpost/slips');
        // Set Helper Model
        $helper = Mage::helper('japanpost');

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
			'after_element_html'=> '<br/><small>'.$helper->__('Update Order Number').'</small>',
            'required'			=> true,
        ));

        // Add Tracking Number Field
    	$fieldset->addField('tracking_number', 'text', array(
            'name'				=> 'tracking_number',
            'label'				=> $helper->__('Tracking Number'),
            'title'				=> $helper->__('Tracking Number'),
			'after_element_html'=> '<br /><small>'.$helper->__('Update Tracking Number').'</small>',
        ));

        // Add Product Number Field
    	$fieldset->addField('product_number', 'text', array(
            'name'				=> 'product_number',
            'label'				=> $helper->__('Product Number'),
            'title'				=> $helper->__('Product Number'),
			'after_element_html'=> '<br /><small>'.$helper->__('Update Product Number').'</small>',
        ));

        // Add Product Name Field
    	$fieldset->addField('product_name', 'text', array(
            'name'				=> 'product_name',
            'label'				=> $helper->__('Product Name'),
            'title'				=> $helper->__('Product Name'),
			'after_element_html'=> '<br /><small>'.$helper->__('Update Product Name').'</small>',
        ));

        // Add Delivery Payment Source Field
    	$fieldset->addField('payment_source', 'select', array(
            'label'				=> $helper->__('Delivery Payment Source'),
            'title'				=> $helper->__('Delivery Payment Source'),
			'after_element_html'=> '<br /><small>'.$helper->__('Update Delivery Payment Source').'</small>',
            'name'				=> 'payment_source',
            'values'			=> $model->getDelPaySourceTypes(),
        ));

        // Add Mail Class Field
    	$fieldset->addField('mail_class', 'select', array(
            'label'				=> $helper->__('Mail Class'),
            'title'				=> $helper->__('Mail Class'),
			'after_element_html'=> '<br /><small>'.$helper->__('Update Mail Class').'</small>',
            'name'				=> 'mail_class',
            'values'			=> $model->getMailClassTypes(),
        ));

        // Add Delivery Services Field
    	$fieldset->addField('ship_service', 'select', array(
            'label'				=> $helper->__('Delivery Services'),
            'title'				=> $helper->__('Delivery Services'),
			'after_element_html'=> '<br /><small>'.$helper->__('Update Delivery Services').'</small>',
            'name'				=> 'ship_service',
            'values'			=> $model->getDelSerTypes(),
        ));

        // Add Ensured Amount Field
        $fieldset->addField('ensured_amount', 'text', array(
            'name'				=> 'ensured_amount',
            'label'				=> $helper->__('Ensured Amount'),
            'class'				=> 'validate-digits validate-length maximum-length-10',
			'after_element_html'=> '<br /><small>'.$helper->__('Update Ensured Amount').'</small>',
        ));

        // Add Discount Type Field
    	$fieldset->addField('discount_type', 'select', array(
            'label'				=> $helper->__('Discount Type'),
            'title'				=> $helper->__('Discount Type'),
			'after_element_html'=> '<br /><small>'.$helper->__('Update Discount Type').'</small>',
            'name'				=> 'discount_type',
            'values'			=> $model->getDiscountTypes(),
        ));

        // Add Taxable Field
    	$fieldset->addField('taxable', 'select', array(
            'label'				=> $helper->__('Taxable'),
            'title'				=> $helper->__('Taxable'),
			'after_element_html'=> '<br /><small>'.$helper->__('Update Taxable').'</small>',
            'name'				=> 'taxable',
            'values'			=> $model->getTaxTypes(),
        ));

        // Add 'Sort Code Field
        $fieldset->addField('sort_code', 'text', array(
            'name'				=> 'sort_code',
            'label'				=> $helper->__('Sort Code'),
            'class'				=> 'validate-digits validate-length maximum-length-10',
			'after_element_html'=> '<br /><small>'.$helper->__('Update Sort Code').'</small>',
        ));

        // Add Cash on Delivery Status Field
    	$fieldset->addField('cod_status', 'select', array(
            'label'				=> $helper->__('Cash on Delivery Status'),
            'title'				=> $helper->__('Cash on Delivery Status'),
			'after_element_html'=> '<br /><small>'.$helper->__('Update Cash on Delivery Status').'</small>',
            'name'				=> 'cod_status',
            'values'			=> $model->getTaxTypes(),
        ));

        // Add Cash on Delivery Amount Field
        $fieldset->addField('cod_amount', 'text', array(
            'name'				=> 'cod_amount',
            'label'				=> $helper->__('Cash on Delivery Amount'),
            'class'				=> 'validate-digits validate-length maximum-length-10',
			'after_element_html'=> '<br /><small>'.$helper->__('Update Cash on Delivery Amount').'</small>',
        ));

		// Finalize the Form
        $form->setValues($registry->getData());
        $this->setForm($form);
        return parent::_prepareForm();
    }
}
