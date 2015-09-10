<?php
/*
 * Irvine Systems Delivery Date Optimum
 *
 * NOTICE OF LICENSE
 * This source file is subject to the Revised BSD License which is bundled with
 * this package in the files LICENSE.txt and LICENSE.html.
 *
 * @category	Magento Catalog Extension
 * @package		IrvineSystems_Deliverydate
 * @author Irvine Systems, Inc.
 * @copyright Copyright (c) 2015, Irvine Systems, Inc. (http://irvinesystems.co.jp/)
 * @license     Revised BSD License
 */
 
class IrvineSystems_Deliverydate_Model_System_Config_Source_Dateformat extends Mage_Core_Model_Config_Data
{
	/**
	 * Get Date Formats Options
	 *
	 * @return array
	*/
	public function toOptionArray()
	{
        return array(
            array('value' => 'd/M/Y', 'label'	=>Mage::helper('deliverydate')->__('Day/Month/Year (Ex. 01/Jan/2015)')),
            array('value' => 'M/d/y', 'label'	=>Mage::helper('deliverydate')->__('Month/Day/Year (Ex. Jan/01/15)')),
            array('value' => 'd-M-Y', 'label'	=>Mage::helper('deliverydate')->__('Day-Month-Year (Ex. 01-Jan-2015)')),
            array('value' => 'Ymd', 'label'		=>Mage::helper('deliverydate')->__('YearMonthDay (Ex. 20150101)')),
            array('value' => 'M-d-y', 'label'	=>Mage::helper('deliverydate')->__('Month-Day-Year (Ex. Jan-01-15)')),
            array('value' => 'm.d.y', 'label'	=>Mage::helper('deliverydate')->__('Month.Day.Year (Ex. 04.01.15)')),
            array('value' => 'd.M.Y', 'label'	=>Mage::helper('deliverydate')->__('Day.Month.Year (Ex. 01.Jan.2015)')),
            array('value' => 'M.d.y', 'label'	=>Mage::helper('deliverydate')->__('Month.Day.Year (Ex. Jan.01.15)')),
            array('value' => 'F j ,Y', 'label'	=>Mage::helper('deliverydate')->__('Month and Day,Year (Ex. January 01 ,2015)')),
            array('value' => 'Y-m-d', 'label'	=>Mage::helper('deliverydate')->__('Standard (Ex. 2015-04-01)')),
            array('value' => 'D M j', 'label'	=>Mage::helper('deliverydate')->__('Week Day (Ex. Mon Jan 01)')),
        );
	}
}