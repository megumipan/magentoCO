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

class IrvineSystems_Deliverydate_Block_Deliverydate_Form extends Mage_Checkout_Block_Onepage_Abstract
{
    /**
     * Block Construction
     * 
     */
	public function __construct()
	{
		// Set the Block Template
		$this->setTemplate('deliverydate/deliverydate/form.phtml');     
	}

    /**
     * Set Delivery Date Data Array
     * 
     * Return Array Structure:
     * 'dayoff'		=> string	list of days off indexes
     * 'minDate'	=> int		Minimum date selectable
     * 'maxDate'	=> int		Maximum Days selection Range
     * 
     * @return array
     * 
     */
	public function getDeliveryData()
	{
		// Check if specifi carriers values are enable
		$isCarActive = Mage::getStoreConfig('deliverydate/carriers/active');
		// Get the dafault delivery data
		$data = $this->_getDefaultData();

		// Get the Specific Shipping Japan Series Delivery Times
		$shippingMethod = $this->_getCarrierMethodCode();
    	$shippingCodes = explode("_",$shippingMethod);
		$hasShippingJapan = Mage::helper('deliverydate')->hasShippingJapan($shippingCodes);
		if($hasShippingJapan){
			$sjHours = Mage::helper($shippingCodes[0])->getDeliveryHours();
			if($sjHours) $data['hours'] = $sjHours;
		}

		// If the Specific setting for cariers are active, check the current carrier and method and retrive its specific data
		if ($isCarActive){
			$carrData = $this->_getMethodData($this->_getCarrierMethodCode());
			if($carrData) $data = $carrData;  
		}
		
		// If a specific method was disable return null so the customer will be inform that no date are available
		if ($data == 'noProcess') return null;
		
		// If no data are available get default values
		//if (!$data) $data = $this->_getDefaultData();
		
		// Check if there is items in the quote which are not in stock
		$notIinStock = $this->_chekStock();
		// Set the First day selectable according to stock level
		if ($notIinStock){
			$minDays = $data['firstNoStock'];
		}else{
			$minDays = $data['firstStock'];
		}

		// Set DayOff Value
		$data['dayoff'] = Mage::getStoreConfig('deliverydate/limiter/dayoff');

		// Count how many day off are selected
		$dayoffNum = explode(",", $data['dayoff']);
		$dayoffNum = count($data['dayoff']);
		
		// Set The Minimum Selectable Date
		$data['minDate'] = $this->_dateToInt($minDays);

		// Set The Maximum Selectable Date
		$data['maxDate'] = $this->_dateToInt($minDays + $data['maxRange'] + $dayoffNum);

		// Unset unused Data
		unset($data['firstNoStock']);
		unset($data['firstStock']);
		unset($data['maxRange']);

		// Set global Data
		return $data;

	}

    /**
     * Check if the Current method has a specific configuration and return its data
     * 
     * Return Array Structure:
     * 'firstStock'		=> int		First Selectable day For in Stock Items
     * 'firstNoStock'	=> int		First Selectable day For Non in Stock Items
     * 'maxRange'		=> int		Maximum Days selection Range
     * 'hours'			=> string	Shipping Hours
     * 
     * @return array
     * 
     */
	protected function _getMethodData($shipMethod)
	{
		// Get all carriers setting from Config
		$methods = Mage::getStoreConfig('deliverydate/carriers/carriers_methods');
		// Stop processing if no data are available
		if (!$methods || $methods =='') return null;
		// Deserialize data
        $methods = unserialize($methods);
		// Check each methods for see if the current is available, if it is retrive its data
		for ($i = 1; $i <= count($methods['method'])-1; $i++) {
		    // if we find a match process the data
			if ($methods['method'][$i] == $shipMethod){
				// Check if the method is actually active, if not return false
				if (!$methods['active'][$i]) return 'noProcess';

				// Exclude first day count to max range
				$maxRange = $methods['maxRange'][$i];
				if($maxRange>-1)$maxRange-=1;
				
				// Set method specifi data
				$data = array(
				    'active'		=> $methods['active'][$i],
				    'firstStock'	=> $methods['firstStock'][$i],
				    'firstNoStock'	=> $methods['firstNoStock'][$i],
				    'maxRange'		=> $maxRange,
				    'hours'			=> $methods['hours'][$i],
				);

				// Return the Data
				return $data;
			}
		}

		// If nothing was found return false
		return null;
	}

    /**
     * Get the Default Delivery Date information from Config
     * 
     * Return Array Structure:
     * 'firstStock'		=> int		First Selectable day For in Stock Items
     * 'firstNoStock'	=> int		First Selectable day For Non in Stock Items
     * 'maxRange'		=> int		Maximum Days selection Range
     * 'hours'			=> string	Shipping Hours
     * 
     * @return array
     * 
     */
	protected function _getDefaultData()
	{
		// Exclude first day count to max range
		$maxRange = Mage::getStoreConfig('deliverydate/limiter/maximum_day');
		if($maxRange>-1)$maxRange-=1;

		// Set data Array
		$data = array(
		    'firstStock'	=> Mage::getStoreConfig('deliverydate/limiter/first_instock_day'),
		    'firstNoStock'	=> Mage::getStoreConfig('deliverydate/limiter/first_notinstock_day'),
		    'maxRange'		=> $maxRange,
		    'hours'			=> Mage::getStoreConfig('deliverydate/limiter/delivery_times'),
		);
		// Return the Data
		return $data;
	}

    /**
     * Check if the Current Quote has products which are not in stock
     * 
     * @return bool
     */
	protected function _chekStock()
	{
		// Get the information for the products in the quote
		$quote = Mage::getSingleton('checkout/session')->getQuote();
		$items = $quote->getItemsCollection()->getItems();

		// Check each items and return true if a non stock item is found
		foreach($items as $item) {
			$product = Mage::getModel('catalog/product');
			$product = $product->load($item->getData('product_id'));
			$qty = $product->getStockItem()->getQty();
			if ($qty <=0) return true;
		}
		// Return False if all items are in stock
		return false;
	}

    /**
     * Check if the Current Quote has products which are not in stock
     * 
     * @param int Days to be added to the current Date
     * 
     * @return int
     */
	protected function _dateToInt($days)
	{
		// Format the date as int value
		$date = date("Ymd", mktime(date("H"), date("i"), date("s"), date("n"), date("j") + $days, date("Y")));
		// Return the formatted value
		return $date;
	}

    /**
     * Get and validate Carrier Method Code
     * 
     * @return string Carrier Method (EX: 'carrierCode_methodCode')
     */
	protected function _getCarrierMethodCode()
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

    /**
     * Get The HTML Option for Time Selection
     * 
     * @return string $hours Selectable Hours List
     * 
     * @return string HTML option code
     */
	public function getTimeHtmlSelect($hours)
	{       
		// Get the currently selected Shipping method
		$shippingMethod = $this->_getCarrierMethodCode();
		// Check if the carrier is part of Shipping Japan Serie
    	$shippingCodes = explode("_",$shippingMethod);
		$hasShippingJapan = Mage::helper('deliverydate')->hasShippingJapan($shippingCodes);
		// Check if the current method is a Shipping Japan Serie
		$options = null;
		// Get the shipping method specific delivery times
		if($hasShippingJapan){
			//$options = Mage::helper($shippingCodes[0])->getDeliveryHours();
		}
		
		if(is_array($hours)) $options = $hours;
		// Set the first empty otion
		$toHtml = '<option value=""></option>';
		// If there are no options available set the default values
		if(!$options){
			$hoursArr = explode(",", $hours);
			// Set the returning html according to the options
			foreach ($hoursArr as $key=>$value) {
				$options[$value] = $value;
			}
		}
	
		// Set the returning html according to the options
		foreach ($options as $key=>$value) {

			// Add the option to the output
			$toHtml .= '<option value="'. $key .'">'. $value .'</option>';
		}
				
		// Return the Option Html
		return $toHtml;
	}
}