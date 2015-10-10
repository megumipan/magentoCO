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
 
class IrvineSystems_Awardpoints_Block_Adminhtml_System_Config_Form_Field_Notice extends Mage_Adminhtml_Block_System_Config_Form_Field
{
   /**
    * Get and Update elment html
    *
    * @param Varien_Data_Form_Element_Abstract $element
    *
    * @return string
    */
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
		// Remove the Scope Label
		$element['scope_label'] = '';
		// Set the notice Message
		$notice = Mage::helper('awardpoints')->__('Please Note that crontab service needs to be enable and configured on your webserver in order for magento  cron jobs to be executed.');
		// Return the Message
        return $notice;
    }
}
