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

class IrvineSystems_JapanPost_Block_Adminhtml_Import_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * Add fieldset
     *
     * @return Mage_ImportExport_Block_Adminhtml_Import_Edit_Form
     */
    protected function _prepareForm()
    {
		// Module Helper
        $helper =  Mage::helper('japanpost');
		// Form Parameter
        $form = new Varien_Data_Form(array(
            'id'      => 'edit_form',
            'action'  => $this->getUrl('*/*/validate'),
            'method'  => 'post',
            'enctype' => 'multipart/form-data'
        ));
        
		// Fieldset Composition
		$fieldset = $form->addFieldset('base_fieldset', array('legend' => $helper->__('Slips Data Import')));

		// File Uploader Field
        $fieldset->addField(IrvineSystems_JapanPost_Model_Import::FIELD_NAME_SOURCE_FILE, 'file', array(
            'name'     => IrvineSystems_JapanPost_Model_Import::FIELD_NAME_SOURCE_FILE,
            'label'    => $helper->__('Select File to Upload'),
            'title'    => $helper->__('Select File to Upload'),
            'required' => true,
			'after_element_html'=> '<br /><small>'.$helper->__('Select the File to be Imported').'</small>'
        ));

		// Email Notification Checkbox
        $fieldset->addField('mail_bool', 'checkbox', array(
            'name'  => 'mail_bool',
            'title' => $helper->__('Send Email Notification'),
            'label' => $helper->__('Send Email Notification'),
            'value' => true,
			'after_element_html'=> '<small>'.$helper->__('Select if an email notification needs to be sent to customers').'</small>'
        ));

		// Set Container and Form Data
        $form->setUseContainer(true);
        $this->setForm($form);

		// Prepare parent form
        return parent::_prepareForm();
    }
}