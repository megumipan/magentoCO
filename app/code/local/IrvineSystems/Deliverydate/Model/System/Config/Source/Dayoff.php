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
 
class IrvineSystems_Deliverydate_Model_System_Config_Source_Dayoff extends Mage_Core_Model_Config_Data
{
	/**
	 * Get Time Days Off Options
	 *
	 * @return array
	*/
	public function toOptionArray()
	{
        return array(
            array('value' => '', 'label'	=>Mage::helper('deliverydate')->__('None')),
            array('value' => 0, 'label'		=>Mage::helper('deliverydate')->__('Sunday')),
            array('value' => 1, 'label'		=>Mage::helper('deliverydate')->__('Monday')),
            array('value' => 2, 'label'		=>Mage::helper('deliverydate')->__('Tuesday')),
            array('value' => 3, 'label'		=>Mage::helper('deliverydate')->__('Wedenesday')),
            array('value' => 4, 'label'		=>Mage::helper('deliverydate')->__('Thursday')),
            array('value' => 5, 'label'		=>Mage::helper('deliverydate')->__('Friday')),
            array('value' => 6, 'label'		=>Mage::helper('deliverydate')->__('Saturday')),
        );
	}
}