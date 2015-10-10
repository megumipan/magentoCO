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
 
class IrvineSystems_Deliverydate_Model_System_Config_Source_Calendarskins extends Mage_Core_Model_Config_Data
{
	/**
	 * Get Skin Style Typoes
	 *
	 * @return array
	*/
	public function toOptionArray()
	{
        return array(
            array('value' => 'blue',		'label'	=>Mage::helper('deliverydate')->__('Blue Skin Type 1')),
            array('value' => 'blue2',		'label'	=>Mage::helper('deliverydate')->__('Blue Skin Type 2')),
            array('value' => 'brown',		'label'	=>Mage::helper('deliverydate')->__('Brown Skin')),
            array('value' => 'green',		'label'	=>Mage::helper('deliverydate')->__('Green Skin')),
            array('value' => 'system',		'label'	=>Mage::helper('deliverydate')->__('System Style Skin')),
            array('value' => 'tas',			'label'	=>Mage::helper('deliverydate')->__('Tas Style Skin')),
            array('value' => 'win2k-1',		'label'	=>Mage::helper('deliverydate')->__('Win2k Style Skin Type 1')),
            array('value' => 'win2k-2',		'label'	=>Mage::helper('deliverydate')->__('Win2k Style Skin Type 2')),
            array('value' => 'win2k-cold-1','label'	=>Mage::helper('deliverydate')->__('Win2k (cold) Style Skin Type 1')),
            array('value' => 'win2k-cold-2','label'	=>Mage::helper('deliverydate')->__('Win2k (cold) Style Skin Type 2')),
            array('value' => 'custom',		'label'	=>Mage::helper('deliverydate')->__('Custom Skin'))
        );
	}
}