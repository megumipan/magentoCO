<?php
/*
 * Irvine Systems Award Points
 *
 * NOTICE OF LICENSE
 * This source file is subject to the Revised BSD License which is bundled with
 * this package in the files LICENSE.txt and LICENSE.html.
 *
 * @category	Magento Sale Extension
 * @package		IrvineSystems_AwardPoints
 * @author Irvine Systems, Inc.
 * @copyright Copyright (c) 2015, Irvine Systems, Inc. (http://irvinesystems.co.jp/)
 * @license     Revised BSD License
 */

class IrvineSystems_Awardpoints_Block_Adminhtml_Stats_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
   /**
    * Prepare Form
    *
    * @return Mage_Adminhtml_Block_Widget_Form
    */
    protected function _prepareForm()
    {
        // Initialize Form
        $form = new Varien_Data_Form(array(
            'id'	=> 'edit_form',
            'action'=> $this->getData('action'),
            'method'=> 'post'
        ));

        // Initialize Check Orders Points Fieldsets
        $fieldset = $form->addFieldset('order_details', array('legend'=>Mage::helper('awardpoints')->__('Date Range for Order Points Check')));

        // Add From Date Field
        $fieldset->addField('from', 'date', array(
            'name'		=> 'from',
            'title'		=> Mage::helper('awardpoints')->__('From Date'),
            'label'		=> Mage::helper('awardpoints')->__('From Date'),
            'image'		=> Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_SKIN).'/adminhtml/default/default/images/grid-cal.gif',
            'format'	=> Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT),
            'required'  => true,
        ));

        // Add To Date Field
        $fieldset->addField('ends', 'date', array(
            'name'		=> 'ends',
            'title'		=> Mage::helper('awardpoints')->__('To Date'),
            'label'		=> Mage::helper('awardpoints')->__('To Date'),
            'image'		=> Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_SKIN).'/adminhtml/default/default/images/grid-cal.gif',
            'format'	=> Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT),
            'required'	=> true,
        ));

		// Finalize the Form
        $form->setUseContainer(true);
        $this->setForm($form);
        return parent::_prepareForm();
    }
}