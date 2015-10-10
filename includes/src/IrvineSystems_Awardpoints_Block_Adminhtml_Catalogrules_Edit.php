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

class IrvineSystems_Awardpoints_Block_Adminhtml_Catalogrules_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
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
        $this->_blockGroup = 'awardpoints';
        $this->_controller = 'adminhtml_catalogrules';

        // Set Types Definition for JavaScript
		$noProcType = Mage::getModel('awardpoints/awardpoints')->getNoProcessType();

        // Javascript to hide and show the Point fields according if they are needed or not
		// TODO: Move Js in a propper place (as design Layout extension)
        $this->_formScripts[] = "
			// Do checking for default values after load
            document.observe('dom:loaded', function() {
                checkTypes();
            });

			// Target selector Function
			function checkTypes(){
                if ($('rule_action_type').getValue() == '".$noProcType."'){
                    $('rule_points').value = '';
                    $('rule_points').up(1).hide();
			        $('rule_points').removeClassName('required-entry');
                } else {
                    $('rule_points').up(1).show();
			        $('rule_points').addClassName('required-entry');
                }
            };
        ";

		// Update Form Container Buttons
        $this->_updateButton('save', 'label', Mage::helper('awardpoints')->__('Save Rule'));
        $this->_updateButton('delete', 'label', Mage::helper('awardpoints')->__('Delete Rule'));
    }

   /**
    * Prepare Block Header
    *
    * @return string
    */
    public function getHeaderText()
    {
        // Check if we are editing a Rule or create a new one
        $rule = Mage::registry('catalogrules_data');
        if ($rule->getRuleId()) {
            return Mage::helper('awardpoints')->__("Edit Rule '%s'", $this->htmlEscape($rule->getTitle()));
        }else{
            return Mage::helper('awardpoints')->__('New Rule');
        }
    }
}