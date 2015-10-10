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
 
class IrvineSystems_Awardpoints_Model_System_Config_Source_Monthdates
{
	/**
	 * Get Time Days Off Options
	 *
	 * @return array
	*/
	public function toOptionArray()
	{
		$options = array();
		for ($i = 1; $i <= 28; $i++) {
            $options[] = array('value' => $i, 'label' => $i);
		}
        return $options;
	}
}