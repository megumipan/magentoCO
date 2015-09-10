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
 
class IrvineSystems_Awardpoints_Block_Referralhead extends Mage_Core_Block_Template
{
    /**
     * Prepare Page Layout
     * 
     */
	protected function _prepareLayout()
    {
        // Prepare Parent Layout
		parent::_prepareLayout();

        // Control if there is Add this account information and add the Javascript for Addthis sharing
		$addThisEnable = Mage::getModel('awardpoints/awardpoints')->getConfig('referral_addthis');
		$addThisAccount = Mage::getModel('awardpoints/awardpoints')->getConfig('referral_addthis_account');
        if ($addThisEnable && $addThisAccount != "")
		{
            // create new base block
			$block = $this->getLayout()->createBlock('Mage_Core_Block_Text');
            // Add the javascript text
            $block->setText('<script type="text/javascript" src="http://s7.addthis.com/js/250/addthis_widget.js#username="'.$addThisAccount.'></script>');
            // append the block to page head
            $this->getLayout()->getBlock('head')->append($block);
        }
    }
}