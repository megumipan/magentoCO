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

class IrvineSystems_Sagawa_Block_Adminhtml_Parcels_Edit_Tab_Delivery extends Mage_Adminhtml_Block_Widget_Form
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
        $fieldset = $form->addFieldset('base_fieldset', array('legend'=>$helper->__('Delivery Information')));

        // Add Shipping Method Field
    	$fieldset->addField('ship_method', 'select', array(
            'label'				=> $helper->__('Shipping Method'),
            'title'				=> $helper->__('Shipping Method'),
			'after_element_html'=> '<br /><small>'.$helper->__('Update Shipping Method').'</small>',
            'name'				=> 'ship_method',
            'values'			=> $model->getShpMethodCodeTypes(),
        ));

        // Add Fragile Products Status Field
    	$fieldset->addField('fragile_status', 'select', array(
            'label'				=> $helper->__('Fragile Products'),
            'title'				=> $helper->__('Fragile Products'),
			'after_element_html'=> '<br /><small>'.$helper->__('Update Fragile Products Status').'</small>',
            'name'				=> 'fragile_status',
            'values'			=> $model->getBoolTypes(),
        ));

        // Add Valuable Products Status Field
    	$fieldset->addField('valuables_status', 'select', array(
            'label'				=> $helper->__('Valuable Products'),
            'title'				=> $helper->__('Valuable Products'),
			'after_element_html'=> '<br /><small>'.$helper->__('Update Valuable Products Status').'</small>',
            'name'				=> 'valuables_status',
            'values'			=> $model->getBoolTypes(),
        ));

        // Add Up Side Specified Status Field
    	$fieldset->addField('side_status', 'select', array(
            'label'				=> $helper->__('Up Side Specified'),
            'title'				=> $helper->__('Up Side Specified'),
			'after_element_html'=> '<br /><small>'.$helper->__('Update Up Side Specified Status').'</small>',
            'name'				=> 'side_status',
            'values'			=> $model->getBoolTypes(),
        ));

        // Add Cooling Shipment Required Field
    	$fieldset->addField('cooling_shipment', 'select', array(
            'label'				=> $helper->__('Cooling Shipment Required'),
            'title'				=> $helper->__('Cooling Shipment Required'),
			'after_element_html'=> '<br /><small>'.$helper->__('Update Cooling Shipment Required Status').'</small>',
            'name'				=> 'cooling_shipment',
            'values'			=> $model->getCoolShipCodeTypes(),
        ));

        // Add Delivery Date Field
        $fieldset->addField('delivery_date', 'text', array(
            'name'				=> 'delivery_date',
            'label'				=> $helper->__('Delivery Date'),
            'class'				=> 'validate-digits validate-length maximum-length-8',
			'after_element_html'=> '<br /><small>'.$helper->__('Update Delivery Date (Ex: 20110101 for 2011/01/01)').'</small>',
        ));

		//Get the type mapping array acording to admin settings
		switch (Mage::getStoreConfig('sagawa/slips/ship_time_class')) {
		    case 0:
		        $timeOptions = $model->getTimeZoneShortTypes();
		        break;
		    default:
		        $timeOptions = $model->getTimeZoneLongTypes();
		        break;
		}
        // Add Delivery Time Field
    	$fieldset->addField('delivery_time', 'select', array(
            'label'				=> $helper->__('Delivery Time'),
            'title'				=> $helper->__('Delivery Time'),
			'after_element_html'=> '<br /><small>'.$helper->__('Update Delivery Time').'</small>',
            'name'				=> 'delivery_time',
            'values'			=> $timeOptions,
        ));

        // Add Delivery Time (Minutes) Field
        $fieldset->addField('delivery_time_min', 'text', array(
            'name'				=> 'delivery_time_min',
            'label'				=> $helper->__('Delivery Time (min)'),
            'class'				=> 'validate-digits validate-length maximum-length-4',
			'after_element_html'=> '<br /><small>'.$helper->__('Update Delivery Time (Minutes) (Ex: 1530 For 15:30)').'</small>',
        ));

        // Add 1st Service Code Field
    	$fieldset->addField('service_code_1', 'select', array(
            'label'				=> $helper->__('1st Service Code'),
            'title'				=> $helper->__('1st Service Code'),
			'after_element_html'=> '<br /><small>'.$helper->__('Update 1st Service Code').'</small>',
            'name'				=> 'service_code_1',
            'values'			=> $model->getServiceCodesTypes(),
        ));

        // Add 2nd Service Code Field
    	$fieldset->addField('service_code_2', 'select', array(
            'label'				=> $helper->__('2nd Service Code'),
            'title'				=> $helper->__('2nd Service Code'),
			'after_element_html'=> '<br /><small>'.$helper->__('Update 2nd Service Code').'</small>',
            'name'				=> 'service_code_2',
            'values'			=> $model->getServiceCodesTypes(),
        ));

        // Add 3rd Service Code Field
    	$fieldset->addField('service_code_3', 'select', array(
            'label'				=> $helper->__('3rd Service Code'),
            'title'				=> $helper->__('3rd Service Code'),
			'after_element_html'=> '<br /><small>'.$helper->__('Update 3rd Service Code').'</small>',
            'name'				=> 'service_code_3',
            'values'			=> $model->getServiceCodesTypes(),
        ));

        // Add Delivery Type Field
    	$fieldset->addField('delivery_type', 'select', array(
            'label'				=> $helper->__('Delivery Type'),
            'title'				=> $helper->__('Delivery Type'),
			'after_element_html'=> '<br /><small>'.$helper->__('Update Delivery Type').'</small>',
            'name'				=> 'delivery_type',
            'values'			=> $model->getDeliveryTypes(),
        ));

        // Add SRC Classification Field
    	$fieldset->addField('src_class', 'select', array(
            'label'				=> $helper->__('SRC Classification'),
            'title'				=> $helper->__('SRC Classification'),
			'after_element_html'=> '<br /><small>'.$helper->__('Update SRC Classification').'</small>',
            'name'				=> 'src_class',
            'values'			=> $model->getSrcClassTypes(),
        ));

        // Add Branch Code Field
        $fieldset->addField('branc_code', 'text', array(
            'name'				=> 'branc_code',
            'label'				=> $helper->__('Branch Code'),
            'class'				=> 'validate-digits validate-length maximum-length-4',
			'after_element_html'=> '<br /><small>'.$helper->__('Update Branch Code').'</small>',
        ));

		// Finalize the Form
        $form->setValues($registry->getData());
        $this->setForm($form);
        return parent::_prepareForm();
    }
}