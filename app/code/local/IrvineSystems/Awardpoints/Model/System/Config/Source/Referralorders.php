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

class IrvineSystems_Awardpoints_Model_System_Config_Source_Referralorders
{
    public function toOptionArray()
    {
        return array(
            array('value' => 1, 'label'=>Mage::helper('awardpoints')->__('First Order Only')),
            array('value' => 2, 'label'=>Mage::helper('awardpoints')->__('Unlimited Orders')),
            array('value' => 3, 'label'=>Mage::helper('awardpoints')->__('Custom Quantity')),
        );
    }

}
