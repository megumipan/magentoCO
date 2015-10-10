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

class IrvineSystems_JapanPost_Block_Adminhtml_Slips_Edit_Tab_Store extends Mage_Adminhtml_Block_Widget_Form
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
        $fieldset = $form->addFieldset('base_fieldset', array('legend'=>$helper->__('Store Information')));

        // Add Delivery Time Field
        $fieldset->addField('store_memberid', 'text', array(
            'name'				=> 'store_memberid',
            'label'				=> $helper->__('Store Member Number'),
			'after_element_html'=> '<br /><small>'.$helper->__('Update Store Member Number').'</small>',
        ));

        // Add Store Prefix Field
    	$fieldset->addField('store_prefix', 'select', array(
            'label'				=> $helper->__('Store Prefix'),
            'title'				=> $helper->__('Store Prefix'),
			'after_element_html'=> '<br /><small>'.$helper->__('Update Store Prefix').'</small>',
            'name'				=> 'store_prefix',
            'values'			=> $model->getPrefixTypes(),
        ));

        // Add Store Name Field
        $fieldset->addField('store_name', 'text', array(
            'name'				=> 'store_name',
            'label'				=> $helper->__('Store Name'),
			'required'			=> true,
			'after_element_html'=> '<br /><small>'.$helper->__('Update Store Name').'</small>',
        ));

        // Add Store Name (Kana) Field
        $fieldset->addField('store_namekana', 'text', array(
            'name'				=> 'store_namekana',
            'label'				=> $helper->__('Store Name (Kana)'),
			'after_element_html'=> '<br /><small>'.$helper->__('Update Store Name (Kana)').'</small>',
        ));

        // Add Store Address Field
        $fieldset->addField('store_address', 'textarea', array(
            'name'				=> 'store_address',
            'label'				=> $helper->__('Store Address'),
            'class'				=> 'validate-length maximum-length-120',
			'required'			=> true,
			'after_element_html'=> '<br /><small>'.$helper->__('Update Store Address').'</small>',
        ));

        // Add Store Postcode Field
        $fieldset->addField('store_postcode', 'text', array(
            'name'				=> 'store_postcode',
            'label'				=> $helper->__('Store Postcode'),
			'required'			=> true,
			'after_element_html'=> '<br /><small>'.$helper->__('Update Store Postcode').'</small>',
        ));

        // Add Store Telephone Field
        $fieldset->addField('store_tel', 'text', array(
            'name'				=> 'store_tel',
            'label'				=> $helper->__('Store Telephone'),
			'after_element_html'=> '<br /><small>'.$helper->__('Update Store Telephone').'</small>',
        ));

        // Add Store Email Field
        $fieldset->addField('store_email', 'text', array(
            'name'				=> 'store_email',
            'label'				=> $helper->__('Store Email'),
            'class'				=> 'validate-email',
			'after_element_html'=> '<br /><small>'.$helper->__('Update Store Email').'</small>',
        ));

		// Finalize the Form
        $form->setValues($registry->getData());
        $this->setForm($form);
        return parent::_prepareForm();
    }
}