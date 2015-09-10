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
 
/**
* The Helper Data Class may be not necessary but it is Mandatory for each Module
*/
class IrvineSystems_Sagawa_Helper_Data extends Mage_Core_Helper_Abstract{
	
    // Helper Constants
    const JAPAN = 'JP'; // Japan Country Code
    const PRICECUR = 'JPY'; // Price List Currency Code
    const CODE = 'sagawa'; // Carrier Code

	/* -------------------------------------------------------- */ 
	/* ----------------- OPTIONAL EXTENSIONS ------------------ */ 
	/* -------------------------------------------------------- */ 
    // Enhanced Cash on Delivery Extension Constants
	const COD_EXT = 'SXJ2aW5lU3lzdGVtc19DYXNob25kZWxpdmVyeQ=='; // Extension Name (IrvineSystems_Cashondelivery)
	const COD_CODE = 'aXJ2aW5lc3lzdGVtc19jYXNob25kZWxpdmVyeQ=='; // Extension Code (irvinesystems_cashondelivery)
    const COD_EXT_ROUTE = 'aXJ2aW5lc3lzdGVtc19jYXNob25kZWxpdmVyeS9jYXNob25kZWxpdmVyeQ=='; // Extension Route (irvinesystems_cashondelivery/cashondelivery)

    // Delivery Date Optimum Extension Constants
    const DD_EXT = 'SXJ2aW5lU3lzdGVtc19EZWxpdmVyeWRhdGU='; // Extension Name (IrvineSystems_Deliverydate)
    const DD_EXT_ROUTE = 'ZGVsaXZlcnlkYXRlL2RlbGl2ZXJ5ZGF0ZQ=='; // Extension Route (deliverydate/deliverydate)

    // Shipping Consolidation Constants
	const SC_EXT = 'SXJ2aW5lU3lzdGVtc19TaGlwcGluZ0NvbnNvbGlkYXRpb24='; // Extension Name (IrvineSystems_ShippingConsolidation)
    const SC_EXT_ROUTE = 'c2hpcHBpbmdjb25zb2xpZGF0aW9uL3BhY2tlcg=='; // Extension Route (shippingconsolidation/packer)
    
	/* -------------------------------------------------------- */ 
	/* -------- CURRENCY CONVERSION HELPER FUNCTIONS ---------- */ 
	/* -------------------------------------------------------- */ 
	
	/**
	* Check if the currency convertion is required.
	*
    * @return bool
	*/
	public function checkCurrency()
	{
		// Get store Base Currency
		$baseCur = Mage::app()->getStore()->getBaseCurrencyCode();
		// Set default result
		$result = false;
		// If the base currency is different from the price list currency set it to true true
		if ($baseCur != self::PRICECUR) $result = true;

		// Return the result
		return $result;
	}

	/**
	* Get the Excange Rate between price list currency and Base Currency
	*
    * @return bool $toBase = convert to base currency flag
    * @return int||float
	*/
	public function getExcRate($toBase = false)
	{
		// Get store Base Currency
		$baseCur = Mage::app()->getStore()->getBaseCurrencyCode();
		// Set default result
		$result = null;
		// Check if the Base currency is different from the Price List Currency
		// If so, the Exchange rate will be updated
		if ($baseCur != self::PRICECUR){
			// If we are using a different base currency most propably we may not have available
			// an Exchange rate from the JPY to the base currency (due to a Magento Bug)
			// for control possible error we will get the exchange rate from the base currency to JPY and reverse it later
			// NOTE: When Magento will fix the bug the function will still work properly,
			// since the solution of the bug  reflect the same logic of this function
			$baseExcRates = Mage::getModel('directory/currency')->getCurrencyRates($baseCur, self::PRICECUR);
			// if an exchange rate is available then we can set the result with the reversed value
			if($baseExcRates) $result = $baseExcRates[self::PRICECUR];
			// Reverse the rate if to Base currency


			if($toBase) $result = 1/$result;
		}
		// Return the result
		return $result;
	}
	
	/**
	* Convert The rate amount by the Exchange Value
	* 
    * @param int||float $rate = Rate to be converted
    * @return bool $toBase = convert to base currency flag
	*/
	public function convertRate($rate,$toBase = false)
	{
		// Get the Exchange rate, if available
		$exchangeRate = $this->getExcRate($toBase);
		
		// Return the rate witout conversion if the exchange rate was not available
		if(!$exchangeRate) return $rate;

		// If the exchange rate is available convert the price
		$result = $rate*$exchangeRate;
		
		// Check if we did actually convert the rate
		// (but not if is base currency, otherwise it will be converted twice)
		if(!$toBase && $result != $rate) $result = $this->adjustAmount($result);

		// round the result to 2f for float currencies
		$result = round($result,2);

 		// Return the result       
		return $result;
	}

	/**
	* Adjust the Amount according to the currency
	*
    * @return int||float
	*/
	private function adjustAmount($value)
	{
		// Set default result
		$result = $value;

		// Get the Current Current Currency
		$currCur = Mage::app()->getStore()->getCurrentCurrencyCode();

		// Round the amount for integer currencies
		if($currCur == 'JPY' || $currCur == 'HUF' || $currCur == 'TWD') $result = round($result);
			
		// Return the result
		return $result;
	}


	/* -------------------------------------------------------- */ 
	/* ------- COOL SHIPPING OPTION HELPER FUNCTIONS ---------- */ 
	/* -------------------------------------------------------- */ 

    /**
     * Validate if cooling shipping can be performed
     * 
     * @return bool
     */
	public function canCool()
	{       
		// Check if the Cool Shipments is enable in configuration
		if (!Mage::getStoreConfig(self::CODE.'/hikyakuexpress/showcool')) return false;
		
		// Get shipping resource
		$resource = Mage::getResourceModel(self::CODE.'/local');
		// Validate if cool shipments can be performed
		$packageSize = Mage::getSingleton(self::CODE.'/session')->getPackageSize();
		$packageWeight = Mage::getSingleton(self::CODE.'/session')->getPackageWeight();
		// Set the result
		$result = $resource->validateCool($packageSize['Vol_Basic'],$packageWeight);
		// Return the result
		return $result;
	}

    /**
     * Get the cool shipment option fee acording to the current Shopping Cart
     * 
     * @return bool
     */
	public function getCoolRate($formatted = false)
	{       
		// Get shipping resource
		$resource = Mage::getResourceModel(self::CODE.'/local');
		// Validate if cool shipments can be performed
		$packageSize = Mage::getSingleton(self::CODE.'/session')->getPackageSize();
		$packageWeight = Mage::getSingleton(self::CODE.'/session')->getPackageWeight();
		$baseRate = $resource->getCoolRate($packageSize['Vol_Basic'],$packageWeight);
		// Convert the Rate
		$rate = $this->convertRate($baseRate,true);

		// Check if we want the String Formatted Price
		if($formatted) $rate = Mage::helper('core')->currency($rate);

		// return the Rate
		return $rate;
	}

	/* -------------------------------------------------------- */ 
	/* ------- CASH ON DELIVERY OPTION HELPER FUNCTIONS ------- */ 
	/* -------------------------------------------------------- */ 

	/**
	 * Get the decoded cod Method Code
	 *
	 * @return String
	 */
	public function getCodMethod()
	{
		// Decode the Cod code
		$result = base64_decode(self::COD_CODE);
		// Return the result
		return $result;
	}
	
    /**
     * Validate if cash on delivery option can be used
     * 
     * @return bool
     */
	public function canCod($currShipment = null)
	{       
		// Get the current shipping method
		if(!$currShipment) $currShipment = Mage::getSingleton('checkout/session')->getQuote()->getShippingAddress()->getShippingMethod();

		// Return false for not compatible shipping methods
		if($currShipment != self::CODE.'_hikyakuexpress') return false;
		
		// Check if the Cod is enable in configuration
		if (!Mage::getStoreConfig(self::CODE.'/hikyakuexpress/showcod')) return false;

		// If enabled, Check if Irvine Systems's Enhanced Cash on Delivery is available and return the result
		$result = Mage::helper('core')->isModuleEnabled(base64_decode(self::COD_EXT));
		
		// Return the result
		return $result;
	}

    /**
     * Get the cash on delivery fee acording to the current Shopping Cart
     * 
     * @return int||float
     */
	public function getCodFee()
	{       
		// Return null if by any chance the cash on delivery is not available
		if(!$this->canCod()) return null;
		
		// Get the Quote instance
		$quote = Mage::getSingleton('checkout/session')->getQuote();
		
		// Check if use the internal Shipping Carrier Cash on Delivery Fee or the standard 
		if (Mage::getStoreConfig(self::CODE.'/hikyakuexpress/internalcod')){
			// Get the Internal Fees
			$fee = $this->getInternalCodFee();
		}else{
			// Get the Cash on deliver Payment fee in base currency and convert it to price list currency
			$fee = $this->convertRate($quote->getBaseCodFee());
		}
		// return the Fee
		return $fee;
	}

	/**
	 * Get the internal shipping method Cash on delivery Fees
	 *
	 * @return int||float
	 */
	public function getInternalCodFee($toBase = false)
	{
		// Do not process if the internal fee are disabled
		if (!Mage::getStoreConfig(self::CODE.'/hikyakuexpress/internalcod')) return null;

		// Get the Quote instance
		/* @var $quote Mage_Sales_Model_Quote */
		$quote = Mage::getSingleton('checkout/session')->getQuote();
		
		// Get shipping resource
		$resource = Mage::getResourceModel(self::CODE.'/local');
		
		// Get subtotal and shipping amount in JPY
		$_subtotal = $this->convertRate($quote->getBaseSubtotal());
		$_shippingAmount = $this->convertRate($quote->getShippingAddress()->getBaseShippingAmount());
		
		// Get the Cod Rate
		$fee = $resource->getCodCharge($_subtotal+$_shippingAmount);

		// Convert the value if needed in base currency
		if($toBase) $fee = $this->convertRate($fee,$toBase);
		
		// return the Fee
		return $fee;
	}
	

	/* -------------------------------------------------------- */ 
	/* ----------- DELIVERY DATE HELPER FUNCTIONS ------------- */ 
	/* -------------------------------------------------------- */ 
	
	/**
	* Return the shipping method specific delivery hours according to configuration
	*
    * @return bool
	*/
	public function getDeliveryHours()
	{
		// Get shipping slips model
		$slipsModel = Mage::getModel(self::CODE.'/slips');
		
		return $slipsModel->getTimesArray();
	}

	
	/* -------------------------------------------------------- */ 
	/* --------- CONVERSION UTILITIES HELPER FUNCTIONS -------- */ 
	/* -------------------------------------------------------- */ 

	/**
	* Convert the Weight accordint to the store Unit of Measure
	* 
    * @param float $weight = Rate to be converted
    * @return float $weight = Converted Rate
	*/
	public function convertWeight($weight)
	{
		// Get the current Store Unit of Measure
		$measureUnit = Mage::getStoreConfig('carriers/'.self::CODE.'/unit_of_measure');

		// get the conversion Divider according to the Unit
		switch($measureUnit){
		    case ('G'): 
				$div = 1;
		    break;
		    case ('KG'):
				$div = 0.001;
		    break;
		    case ('LB'):
				$div = 0.00220462;
		    break;
		}
			
		// Convert the Weight accorfing to the Unit
		$weight /= $div;
			
		// Return the Converted Weight
		return $weight;
	}

	
	/* -------------------------------------------------------- */ 
	/* ------- TEMPLATE MASTER FIRECHECKOUT INTEGRATION ------- */ 
	/* -------------------------------------------------------- */ 

    /**
     * Public Getter for Firecheckout State
     * 
     */
	public function hasFirecheckout()
	{
        // Check if the extension is disable or its rendering is disabled
		if (!Mage::helper('core')->isModuleEnabled('TM_FireCheckout')) return false;
		if (!Mage::helper('core')->isModuleOutputEnabled('TM_FireCheckout')) return false;
		if (!Mage::getStoreConfig('firecheckout/general/enabled')) return false;
		// Otherwise return true
		return true; 
	}
}