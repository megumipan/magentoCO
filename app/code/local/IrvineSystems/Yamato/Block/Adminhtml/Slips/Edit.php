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

class IrvineSystems_Yamato_Block_Adminhtml_Slips_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
   /**
    * Block constructor, prepare form Container params
    *
    */
    public function __construct()
    {
		// Construct Parent Container
        parent::__construct();

        // Set Block Data
		$this->_objectId = 'id';
        $this->_blockGroup = 'yamato';
        $this->_controller = 'adminhtml_slips';

		// Remove Delete button and update Save button
		$this->_removeButton('delete');
        $this->_updateButton('save', 'label', Mage::helper('yamato')->__('Save Slip'));

        // Javascript to hide and fields in the tabs
        $this->_formScripts[] = "
			// Do checking for default values after load
            document.observe('dom:loaded', function() {
                checkNoticeSchedule();
                checkNoticeComplete();
            });
			
			// Switch available fields for Notice Schedule
			function checkNoticeSchedule(){
				if ($('slip_enable_email_notice_schedule').getValue() == 1){
                    $('slip_input_equipment').parentNode.parentNode.show();
                    $('slip_email_notice_schedule').parentNode.parentNode.show();
			        $('slip_email_notice_schedule').addClassName('required-entry').addClassName('validate-email');
                    $('slip_email_message_notice_schedule').parentNode.parentNode.show();
			        $('slip_email_message_notice_schedule').addClassName('required-entry').addClassName('validate-length maximum-length-148');
                } else {
                    $('slip_input_equipment').parentNode.parentNode.hide();
                    $('slip_email_notice_schedule').parentNode.parentNode.hide();
			        $('slip_email_notice_schedule').removeClassName('required-entry').removeClassName('validate-email');
                    $('slip_email_message_notice_schedule').parentNode.parentNode.hide();
			        $('slip_email_message_notice_schedule').removeClassName('required-entry').removeClassName('validate-length maximum-length-148');
                }                
            };           

			// Switch available fields for Notice Complete
			function checkNoticeComplete(){
				if ($('slip_enable_email_notice_complete').getValue() == 1){
                    $('slip_email_notice_complete').parentNode.parentNode.show();
                    $('slip_email_notice_complete').addClassName('required-entry').addClassName('validate-email');
                    $('slip_email_message_notice_complete').parentNode.parentNode.show();
                    $('slip_email_message_notice_complete').addClassName('required-entry').addClassName('validate-length maximum-length-148');
                } else {
                    $('slip_email_notice_complete').parentNode.parentNode.hide();
                    $('slip_email_notice_complete').removeClassName('required-entry').removeClassName('validate-email');
                    $('slip_email_message_notice_complete').parentNode.parentNode.hide();
                    $('slip_email_message_notice_complete').removeClassName('required-entry').removeClassName('validate-length maximum-length-148');
                }                
            };           

        ";
    }

   /**
    * Prepare Block Header
    *
    * @return string
    */
    public function getHeaderText()
    {
        // Check if we are editing a Rule or create a new one
		$slip = Mage::registry('slip_data');
        return Mage::helper('yamato')->__("Edit Shipping Slip for Order '%s'", $this->htmlEscape($slip->getOrderId()));
    }
}