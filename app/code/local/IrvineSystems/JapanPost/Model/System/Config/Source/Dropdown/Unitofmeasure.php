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

class IrvineSystems_JapanPost_Model_System_Config_Source_Dropdown_Unitofmeasure
{
    public function toOptionArray()
    {
		return array(
            array('value' => 'G', 'label'=>Mage::helper('japanpost')->__('Grams')),
            array('value' => 'KG', 'label'=>Mage::helper('japanpost')->__('Kilograms')),
            array('value' => 'LB', 'label'=>Mage::helper('japanpost')->__('Pounds'))
        );
    }
}