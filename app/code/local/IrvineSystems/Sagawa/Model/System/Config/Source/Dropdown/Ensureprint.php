<?php
/*
 * Irvine Systems Shipping Japan Sgw
 *
 * NOTICE OF LICENSE
 * This source file is subject to the Revised BSD License which is bundled with
 * this package in the files LICENSE.txt and LICENSE.html.
 *
 * @category	Magento Shipping Extension
 * @package		IrvineSystems_Sagawa
 * @author Irvine Systems, Inc.
 * @copyright Copyright (c) 2015, Irvine Systems, Inc. (http://irvinesystems.co.jp/)
 * @license     Revised BSD License
 */

class IrvineSystems_Sagawa_Model_System_Config_Source_Dropdown_Ensureprint
{
    public function toOptionArray()
    {
		// Get Ensured Printing Mode Array
		$prefix = Mage::getModel('sagawa/slips')->getEnsuredPrintTypes();

		// Set the Option according to the Array Values
		foreach ($prefix as $key=>$option) {
			$options[] = array('value' => $key, 'label'=>$option);
		}
		// Return the options list
		return $options;
    }
}