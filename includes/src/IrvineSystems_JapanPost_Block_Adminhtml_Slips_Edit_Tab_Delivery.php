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

class IrvineSystems_JapanPost_Block_Adminhtml_Slips_Edit_Tab_Delivery extends Mage_Adminhtml_Block_Widget_Form
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
        $model = Mage::getModel('japanpost/slips');
        // Set Helper Model
        $helper = Mage::helper('japanpost');

        // Initialize Form
        $form = new Varien_Data_Form();
        // assifgn a prefix to the subform
        $form->setHtmlIdPrefix('slip_');

		// Initialize Actions Fieldsets
        $fieldset = $form->addFieldset('base_fieldset', array('legend'=>$helper->__('Delivery Information')));

        // Add Package Weight (g) Field
        $fieldset->addField('package_weight', 'text', array(
            'name'				=> 'package_weight',
            'label'				=> $helper->__('Package Weight (g)'),
            'class'				=> 'validate-digits validate-length maximum-length-5',
			'required'			=> true,
			'after_element_html'=> '<br /><small>'.$helper->__('Update Package Weight (g)').'</small>',
        ));

        // Add Package Size (mm) Field
        $fieldset->addField('package_size', 'text', array(
            'name'				=> 'package_size',
            'label'				=> $helper->__('Package Size (mm)'),
            'class'				=> 'validate-digits validate-length maximum-length-10',
			'required'			=> true,
			'after_element_html'=> '<br /><small>'.$helper->__('Update Package Size (mm) the size equals to L+W+D').'</small>',
        ));

        // Add Has Fragile Field
    	$fieldset->addField('fragile_status', 'select', array(
            'label'				=> $helper->__('Has Fragile Items'),
            'title'				=> $helper->__('Has Fragile Items'),
			'after_element_html'=> '<br /><small>'.$helper->__('Update Has Fragile Items Status').'</small>',
            'name'				=> 'fragile_status',
            'values'			=> $model->getBoolTypes(),
        ));

        // Add Has Creature Field
    	$fieldset->addField('creature_status', 'select', array(
            'label'				=> $helper->__('Has Creature'),
            'title'				=> $helper->__('Has Creature'),
			'after_element_html'=> '<br /><small>'.$helper->__('Update Has Creature Status').'</small>',
            'name'				=> 'creature_status',
            'values'			=> $model->getBoolTypes(),
        ));

        // Add Has Creature Field
    	$fieldset->addField('glass_status', 'select', array(
            'label'				=> $helper->__('Has Glass'),
            'title'				=> $helper->__('Has Glass'),
			'after_element_html'=> '<br /><small>'.$helper->__('Update Has Glass Status').'</small>',
            'name'				=> 'glass_status',
            'values'			=> $model->getBoolTypes(),
        ));

        // Add Can't Rotate Box Field
    	$fieldset->addField('side_status', 'select', array(
            'label'				=> $helper->__("Can't Rotate Box"),
            'title'				=> $helper->__("Can't Rotate Box"),
			'after_element_html'=> '<br /><small>'.$helper->__("Update Can't Rotate Box Status").'</small>',
            'name'				=> 'side_status',
            'values'			=> $model->getBoolTypes(),
        ));

        // Add Can't Put Weight Field
    	$fieldset->addField('weight_status', 'select', array(
            'label'				=> $helper->__("Can't Put Weight"),
            'title'				=> $helper->__("Can't Put Weight"),
			'after_element_html'=> '<br /><small>'.$helper->__("Update Can't Put Weight Status").'</small>',
            'name'				=> 'weight_status',
            'values'			=> $model->getBoolTypes(),
        ));

        // Add Cooling Shipment Required Field
    	$fieldset->addField('ship_cooler', 'select', array(
            'label'				=> $helper->__('Cooling Shipment Required'),
            'title'				=> $helper->__('Cooling Shipment Required'),
			'after_element_html'=> '<br /><small>'.$helper->__('Update Cooling Shipment Required Status').'</small>',
            'name'				=> 'ship_cooler',
            'values'			=> $model->getBoolTypes(),
        ));

        // Add Delivery Mode Field
    	$fieldset->addField('delivery_mode', 'select', array(
            'label'				=> $helper->__('Delivery Mode'),
            'title'				=> $helper->__('Delivery Mode'),
			'after_element_html'=> '<br /><small>'.$helper->__('Update Delivery Mode').'</small>',
            'name'				=> 'delivery_mode',
            'values'			=> $model->getDelModTypes(),
        ));

        // Add Delivery Date Field
        $fieldset->addField('shipping_date', 'date', array(
            'name'		=> 'shipping_date',
            'title'		=> $helper->__('Estimate Shipping Date'),
            'label'		=> $helper->__('Estimate Shipping Date'),
            'image'		=> Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_SKIN).'/adminhtml/default/default/images/grid-cal.gif',
            'format'	=> Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT),
        ));

        // Add Delivery Date Field
        $fieldset->addField('delivery_date', 'date', array(
            'name'		=> 'delivery_date',
            'title'		=> $helper->__('Delivery Date'),
            'label'		=> $helper->__('Delivery Date'),
            'image'		=> Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_SKIN).'/adminhtml/default/default/images/grid-cal.gif',
            'format'	=> Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT),
        ));

        // Add Delivery Time Field
        $fieldset->addField('delivery_time', 'text', array(
            'name'				=> 'delivery_time',
            'label'				=> $helper->__('Delivery Time'),
			'after_element_html'=> '<br /><small>'.$helper->__('Update Delivery Time').'</small>',
        ));

        // Add Delivery Type Field
    	$fieldset->addField('delivery_type', 'select', array(
            'label'				=> $helper->__('Delivery Type'),
            'title'				=> $helper->__('Delivery Type'),
			'after_element_html'=> '<br /><small>'.$helper->__('Update Delivery Type').'</small>',
            'name'				=> 'delivery_type',
            'values'			=> $model->getDelTypes(),
        ));

        // Add Delivery Comment Field
        $fieldset->addField('delivery_comment', 'textarea', array(
            'name'				=> 'delivery_comment',
            'label'				=> $helper->__('Delivery Comment'),
            'class'				=> 'validate-length maximum-length-60',
			'after_element_html'=> '<br /><small>'.$helper->__('Update Delivery Comment').'</small>',
        ));

        // Add Free Field Field
        $fieldset->addField('free_field2', 'textarea', array(
            'name'				=> 'free_field2',
            'label'				=> $helper->__('Free Field'),
            'class'				=> 'validate-length maximum-length-60',
			'after_element_html'=> '<br /><small>'.$helper->__('Optional Free Field For Additional Notes').'</small>',
        ));

        // Add Free Field Field
        $fieldset->addField('free_field3', 'textarea', array(
            'name'				=> 'free_field3',
            'label'				=> $helper->__('Free Field'),
            'class'				=> 'validate-length maximum-length-60',
			'after_element_html'=> '<br /><small>'.$helper->__('Optional Free Field For Additional Notes').'</small>',
        ));

        // Add Free Field Field
        $fieldset->addField('free_field4', 'textarea', array(
            'name'				=> 'free_field4',
            'label'				=> $helper->__('Free Field'),
            'class'				=> 'validate-length maximum-length-60',
			'after_element_html'=> '<br /><small>'.$helper->__('Optional Free Field For Additional Notes').'</small>',
        ));

        // Add Free Field Field
        $fieldset->addField('free_field5', 'textarea', array(
            'name'				=> 'free_field5',
            'label'				=> $helper->__('Free Field'),
            'class'				=> 'validate-length maximum-length-60',
			'after_element_html'=> '<br /><small>'.$helper->__('Optional Free Field For Additional Notes').'</small>',
        ));

		// Finalize the Form
        $form->setValues($registry->getData());
        $this->setForm($form);
        return parent::_prepareForm();
    }
}