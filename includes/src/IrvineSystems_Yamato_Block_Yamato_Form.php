<?php
/*
 * Irvine Systems Shipping Japan Ymt
 *
 * NOTICE OF LICENSE
 * This source file is subject to the Revised BSD License which is bundled with
 * this package in the files LICENSE.txt and LICENSE.html.
 *
 * @category	Magento Shipping Extension
 * @package		IrvineSystems_Yamato
 * @author Irvine Systems, Inc.
 * @copyright Copyright (c) 2015, Irvine Systems, Inc. (http://irvinesystems.co.jp/)
 * @license     Revised BSD License
 */

class IrvineSystems_Yamato_Block_Yamato_Form extends Mage_Checkout_Block_Onepage_Abstract
{
    /**
     * Block Construction
     * 
     */
	public function __construct()
	{
		// Set the Block Template
		$this->setTemplate('yamato/yamato/form.phtml');
	}

    /**
     * Get The HTML Option for Bool Selection
     * 
     * @return string HTML code
     */
	public function canRender()
	{       
		// Get the current Carrier
		$carrier = explode('_',$this->getCarrierMethodCode());
		// Return true if the carrier is correct
		if ($carrier[0]=='yamato') return true;
		// Otherwise return False
		return false;
	}

    /**
     * Validate if the handling options are available
     * 
     * @return string HTML code
     */
	public function canHandle()
	{       
		// Get the current Carrier
		$carrier = explode('_',$this->getCarrierMethodCode());
		// Return true if the method is correct
		if ($carrier[1]=='taqbin') return true;
		// Otherwise return False
		return false;
	}

    /**
     * Validate if the notification options are available
     * 
     * @return string HTML code
     */
	public function canNotify()
	{       
		// Get the current Carrier
		$carrier = explode('_',$this->getCarrierMethodCode());
		// Return true if the method is correct
		if ($carrier[1]=='taqbin') return true;
		// Otherwise return False
		return false;
	}

    /**
     * Get The HTML Option for Bool Selection
     * 
     * @return string HTML code
     */
	public function getBoolHtmlSelect()
	{       
		// Get Bool Types Array
		$values = Mage::getModel('yamato/slips')->getBoolTypes();
		// Reset Html String
		$toHtml = '';
		// Set the Option according to the Array Values
		foreach ($values as $key=>$value) {
		   $toHtml .= '<option value="'. $key .'">'. $value .'</option>';
		}

		// Return HTML string
		return $toHtml;
	}

    /**
     * Get The HTML Option for Delivery Types Selection
     * 
     * @return string HTML code
     */
	public function getDeliveryHtmlSelect()
	{       
		// Get Bool Types Array
		$values = Mage::getModel('yamato/slips')->getDelTypes();
		// Reset Html String
		$toHtml = '';
		// Selected Value
		$sel='';
		// Set the Option according to the Array Values
		foreach ($values as $key=>$value) {
		   if ($key == 1) $sel ='selected';
		   $toHtml .= '<option value="'. $key .'"'.$sel.'>'. $value .'</option>';
		   $sel='';
		}
		// Return HTML string
		return $toHtml;
	}

    /**
     * Validate if express shipping can be performed
     * 
     * @return bool
     */
	public function canExpress()
	{       
		// Get the current Carrier
		$carrier = explode('_',$this->getCarrierMethodCode());
		// Get the helper
		$helper = Mage::helper('yamato');
		return $helper->canExpress($carrier[1]);
	}

    /**
     * Get the express service fee
     * 
     * @return int||float
     */
	public function getExpressRate()
	{       
		// Get the helper
		$helper = Mage::helper('yamato');
		return $helper->getExpressRate(true);
	}
	

    /**
     * Validate if cooling shipping can be performed
     * 
     * @return bool
     */
	public function canCool()
	{       
		// Get the current Carrier
		$carrier = explode('_',$this->getCarrierMethodCode());
		// Get the helper
		$helper = Mage::helper('yamato');
		return $helper->canCool($carrier[1]);
	}

    /**
     * Get the Cool rate fee for the current cart
     * 
     * @return int||float
     */
	public function getCoolRate()
	{       
		// Get the helper
		$helper = Mage::helper('yamato');
		return $helper->getCoolRate(true);
	}
	
    /**
     * Get The HTML Option for Cooling Shipment Methods Methods Types Selection
     * 
     * @return string HTML code
     */
	public function getCoolShipHtmlSelect()
	{       
		// Get Bool Types Array
		$values = Mage::getModel('yamato/slips')->getCoolShipCodeTypes();
		// Reset Html String
		$toHtml = '';
		// Set the Option according to the Array Values
		foreach ($values as $key=>$value) {
			if ($key > 0) $toHtml .= '<option value="'. $key .'">'. $value .'</option>';
		}
		// Return HTML string
		return $toHtml;
	}

    /**
     * Get and validate Carrier Method Code
     * 
     * @return string Carrier Method (EX: 'carrierCode_methodCode')
     */
	public function getCarrierMethodCode()
    {
        // Check if the Shippinbg code was set from Ajax, if not set the current selected Method
        if(!$this->hasShippingCode()){
            $this->setShippingCode($this->getQuote()->getShippingAddress()->getShippingMethod());
		}
 
        // Validate shipping code and return it
        if(trim($this->getShippingCode()) != '') return $this->getShippingCode();

		// Return False, if the shipping code was invalid
        return false;
    }	
}