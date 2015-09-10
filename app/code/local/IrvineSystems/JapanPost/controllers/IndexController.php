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

class IrvineSystems_JapanPost_IndexController extends Mage_Core_Controller_Front_Action
{
	/**
     * AJAX action Controller
     * 
     */
    public function AjaxAction()
    {
		// Get the selected shipping Method
		$shippingCode = $this->getRequest()->getPost('shipping_code');
		// Set The Carrier Code
		$carrierCode = explode('_',$shippingCode);
		$carrierCode = $carrierCode[0];

		// Stop processing the request if the Ajax for the method is not enable
		if(!Mage::getStoreConfig($carrierCode.'/slips/ajaxenable')) return null;

		// Create form Block
		$formBlock = $this->getLayout()->createBlock($carrierCode.'/'.$carrierCode.'_form');
		// Set the selected shipping method into the block
		$formBlock->setShippingCode($shippingCode);
		// Get Block HTML code
		$content = $formBlock->toHtml();
		// Get the body for layout render
		$this->getResponse()->setBody(Mage::helper('core')->jsonEncode(array('optionsformhtml' => $content)));
    }
}