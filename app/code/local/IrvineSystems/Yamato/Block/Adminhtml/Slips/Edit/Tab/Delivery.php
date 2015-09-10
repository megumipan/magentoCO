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

class IrvineSystems_Yamato_Block_Adminhtml_Slips_Edit_Tab_Delivery extends Mage_Adminhtml_Block_Widget_Form
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
        $fieldset = $form->addFieldset('base_fieldset', array('legend'=>$helper->__('Delivery Information')));

        // Add Delivery Mode Field
    	$fieldset->addField('delivary_mode', 'select', array(
            'name'				=> 'delivary_mode',
			'label'				=> $helper->__('Delivery Mode'),
            'title'				=> $helper->__('Delivery Mode'),
			'after_element_html'=> '<br /><small>'.$helper->__('Update Delivery Mode').'</small>',
            'values'			=> $model->getDelivaryModeTypes(),
        ));

        // Add Delivery Mode Field
    	$fieldset->addField('cool_type', 'select', array(
            'name'				=> 'cool_type',
			'label'				=> $helper->__('Cool Type'),
            'title'				=> $helper->__('Cool Type'),
			'after_element_html'=> '<br /><small>'.$helper->__('Update Cool Type').'</small>',
            'values'			=> $model->getCoolShipCodeTypes(),
        ));

        // Add Slip Number Field
        $fieldset->addField('slip_number', 'text', array(
            'name'				=> 'slip_number',
            'label'				=> $helper->__('Slip Number'),
            'class'				=> 'validate-length maximum-length-12',
			'after_element_html'=> '<br /><small>'.$helper->__('Update Slip Number').'</small>',
        ));

        // Add Shipment Date Field
        $fieldset->addField('shipment_date', 'text', array(
            'name'				=> 'shipment_date',
            'label'				=> $helper->__('Shipment Date'),
			'required'			=> true,
            'class'				=> 'validate-length maximum-length-10',
			'after_element_html'=> '<br /><small>'.$helper->__('Update Shipment Date').'</small>',
        ));

        // Add Delivery Date Field
        $fieldset->addField('delivery_date', 'text', array(
            'name'				=> 'delivery_date',
            'label'				=> $helper->__('Delivery Date'),
            'class'				=> 'validate-length maximum-length-10',
			'after_element_html'=> '<br /><small>'.$helper->__('Update Delivery Date').'</small>',
        ));

        // Add Delivery Time Field
    	$fieldset->addField('delivery_time', 'select', array(
            'name'				=> 'delivery_time',
			'label'				=> $helper->__('Delivery Time'),
            'title'				=> $helper->__('Delivery Time'),
			'after_element_html'=> '<br /><small>'.$helper->__('Update Delivery Time').'</small>',
            'values'			=> $model->getTimeZoneLongTypes(),
        ));

		// Add Product ID 1 Field
        $fieldset->addField('product_id_1', 'text', array(
            'name'				=> 'product_id_1',
            'label'				=> $helper->__('Product ID 1'),
            'class'				=> 'validate-length maximum-length-30',
			'after_element_html'=> '<br /><small>'.$helper->__('Update Product ID 1').'</small>',
        ));

        // Add Product Name 1 Field
        $fieldset->addField('product_name_1', 'text', array(
            'name'				=> 'product_name_1',
            'label'				=> $helper->__('Product Name 1'),
			'required'			=> true,
            'class'				=> 'validate-length maximum-length-50',
			'after_element_html'=> '<br /><small>'.$helper->__('Update Product Name 1').'</small>',
        ));

        // Add Product ID 2 Field
        $fieldset->addField('product_id_2', 'text', array(
            'name'				=> 'product_id_2',
            'label'				=> $helper->__('Product ID 2'),
            'class'				=> 'validate-length maximum-length-30',
			'after_element_html'=> '<br /><small>'.$helper->__('Update Product ID 2').'</small>',
        ));

        // Add Product Name 2 Field
        $fieldset->addField('product_name_2', 'text', array(
            'name'				=> 'product_name_2',
            'label'				=> $helper->__('Product Name 2'),
            'class'				=> 'validate-length maximum-length-50',
			'after_element_html'=> '<br /><small>'.$helper->__('Update Product Name 2').'</small>',
        ));

        // Add Handling 1 Field
        $fieldset->addField('handling_1', 'text', array(
            'name'				=> 'handling_1',
            'label'				=> $helper->__('Handling 1'),
            'class'				=> 'validate-length maximum-length-20',
			'after_element_html'=> '<br /><small>'.$helper->__('Update Handling 1').'</small>',
        ));

        // Add Handling 2 Field
        $fieldset->addField('handling_2', 'text', array(
            'name'				=> 'handling_2',
            'label'				=> $helper->__('Handling 2'),
            'class'				=> 'validate-length maximum-length-20',
			'after_element_html'=> '<br /><small>'.$helper->__('Update Handling 2').'</small>',
        ));

        // Add Comment Field
        $fieldset->addField('comment', 'text', array(
            'name'				=> 'comment',
            'label'				=> $helper->__('Comment'),
            'class'				=> 'validate-length maximum-length-32',
			'after_element_html'=> '<br /><small>'.$helper->__('Update Comment').'</small>',
        ));

        // Add Cash on Delivery Amount(including tax) Field
        $fieldset->addField('cod_amount', 'text', array(
            'name'				=> 'cod_amount',
            'label'				=> $helper->__('Cash on Delivery Amount(including tax)'),
			'required'			=> true,
            'class'				=> 'validate-length maximum-length-7',
			'after_element_html'=> '<br /><small>'.$helper->__('Update Cash on Delivery Amount(including tax)').'</small>',
        ));

        // Add Amount of Tax Field
        $fieldset->addField('tax_amount', 'text', array(
            'name'				=> 'tax_amount',
            'label'				=> $helper->__('Amount of Tax'),
			'required'			=> true,
            'class'				=> 'validate-length maximum-length-7',
			'after_element_html'=> '<br /><small>'.$helper->__('Update Amount of Tax').'</small>',
        ));

        // Add Held at Yamato Office Field
        $fieldset->addField('held_yamato_office', 'text', array(
            'name'				=> 'held_yamato_office',
            'label'				=> $helper->__('Held at Yamato Office'),
            'class'				=> 'validate-length maximum-length-1',
			'after_element_html'=> '<br /><small>'.$helper->__('Update Held at Yamato Office').'</small>',
        ));

        // Add Yamato Office ID Field
        $fieldset->addField('yamato_office_id', 'text', array(
            'name'				=> 'yamato_office_id',
            'label'				=> $helper->__('Yamato Office ID'),
			'required'			=> true,
            'class'				=> 'validate-length maximum-length-6',
			'after_element_html'=> '<br /><small>'.$helper->__('Update Yamato Office ID').'</small>',
        ));

        // Add Number of Issued Field
        $fieldset->addField('number_of_issued', 'text', array(
            'name'				=> 'number_of_issued',
            'label'				=> $helper->__('Number of Issued'),
            'class'				=> 'validate-length maximum-length-2',
			'after_element_html'=> '<br /><small>'.$helper->__('Update Number of Issued').'</small>',
        ));

        // Add The Number Display Flag Field
        $fieldset->addField('number_display_flag', 'text', array(
            'name'				=> 'number_display_flag',
            'label'				=> $helper->__('The Number Display Flag'),
            'class'				=> 'validate-length maximum-length-1',
			'after_element_html'=> '<br /><small>'.$helper->__('Update The Number Display Flag').'</small>',
        ));
        
		// Finalize the Form
        $form->setValues($registry->getData());
        $this->setForm($form);
        return parent::_prepareForm();
    }
}