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

class IrvineSystems_Yamato_Model_Carrier_Yamato extends Mage_Shipping_Model_Carrier_Abstract
{
	// Model Constants
    const JAPAN = 'JP'; // Japan Country Code
    const PRICECUR = 'JPY'; // Price List Currency Code
    const CODE = 'yamato'; // Carrier Code

    // Carrier Tracking Constants
	// Todo: track for locale
	const TRACK_URL = 'http://toi.kuronekoyamato.co.jp/cgi-bin/tneko';
    // Tracker Avvailable Languages
	protected $_langUrl = array('en','ja');

	/**
	 * Public Getter for Carrier Code
	 */
	protected $_code = self::CODE;

    // exchange Rate from Base currency to price List Currency
    protected $_exchangeRate = null;
	
    // exchange Rate from Base currency to price List Currency
    protected $_exchangeRequired = false;
	
	// Final Volumetric consolidated Dimensions
	protected $_finalVolumes = null;

	// Total Weight of the Shopping Cart
	protected $_cartWeight = null;

	// Supported Methods Array
	protected $_locMethodCode = array('taqbin','mailbin');
	
	// Shipping origin/Destination Parameters
	protected $_dstCountryId = null;
	protected $_srcProvinceId = null;
	protected $_dstProvinceId = null;

	// Result object for the Shipping
	protected $_result = null;

	// Error object for the Shipping
	protected $_error = null;

	// Bool for the special price
	protected $_special = false;

	/**
	* Collect rates for this shipping method based on information in $request
	*
	* @param Mage_Shipping_Model_Rate_Request $data
	* @return Mage_Shipping_Model_Rate_Result
	*/
	public function collectRates(Mage_Shipping_Model_Rate_Request $request)
	{
		// Exit if the carrier is not enabled
		if (!Mage::getStoreConfig('carriers/'.self::CODE.'/active')) return false;
		
		// Exit if the country is not supported
		if ($request->getDestCountryId() != self::JAPAN) return false;
		
		// Set destination Country and Merchant Prefecture
		$this->_dstCountryId = $request->getDestCountryId();
        $this->_dstProvinceId = $request->getDestRegionId();
        $this->_srcProvinceId = $request->getRegionId();

		// Set Shipping integration
		$this->_result = Mage::getModel('shipping/rate_result');
       	$this->_error = Mage::getModel('shipping/rate_result_error');

		// Set Carrier information
        $this->_error->setCarrier(self::CODE);
        $this->_error->setCarrierTitle($this->getConfigData('title'));

		// Instance the extension helper
		$helper = Mage::helper(self::CODE);
		
		// Check if the Currency exchange is required.
		$this->_exchangeRequired = $helper->checkCurrency();
		// If the exchange is required geth the exchange rate
		if($this->_exchangeRequired) $this->_exchangeRate = $helper->getExcRate();

		// If the exhange rate is required but there is no actual exchange Rate, it will be impossible to process the carrier.
		// Therefore better stop now and send an error message in Front-end
        if($this->_exchangeRequired && !$this->_exchangeRate) {
			$this->_error->setErrorMessage($this->getErrorMsg('noExchange', array(Mage::app()->getStore()->getBaseCurrencyCode(),self::PRICECUR))); 
           $this->_result->append($this->_error);
           return $this->_result;
        }

        // Set Base Parameters
        $maxWeight = Mage::getStoreConfig(self::CODE.'/general/max_package_weight');

        // Check if Order is too Heavy for the Carrier, both local and international shipping have the
		// same Weight Limit, so we can check it right away.
		// If the weight is invalid inform the customer
        $this->_cartWeight = $helper->convertWeight($request->getPackageWeight());
        if($this->_cartWeight > $maxWeight) {
           $this->_error->setErrorMessage($this->getErrorMsg('overWeight')); 
           $this->_result->append($this->_error);
           return $this->_result;
        }

		// For local shipping the destination region is mandatory.
		// before proceed let's be sure the region is selected for this countries 
		// If there is no prefecture inform the customer
        if($this->_dstCountryId == self::JAPAN && !$this->_dstProvinceId) {
           $this->_error->setErrorMessage($this->getErrorMsg('noProvince')); 
           $this->_result->append($this->_error);
           return $this->_result;
        }

		// Check if consolidation Engine is available
		$havePack = Mage::helper('core')->isModuleEnabled(base64_decode($helper::SC_EXT));
		// If it is available use the extension packer, otherwise use the internal base packing
		if($havePack){
			$this->_finalVolumes = Mage::getModel(base64_decode($helper::SC_EXT_ROUTE))->packStrut();
		}else{
			$this->_finalVolumes = Mage::getResourceModel(self::CODE.'/data')->packStrut();
		}

		// Set the package size and weight information in the current session
		Mage::getSingleton(self::CODE.'/session')->setPackageSize($this->_finalVolumes);
		Mage::getSingleton(self::CODE.'/session')->setPackageWeight($this->_cartWeight);

		// If there are no measure in the database we cannot process properly the Carrier.
		// Therefore better stop now and send an error message in Front-end
        if(!$this->_finalVolumes || $this->_finalVolumes['Vol_Basic'] == 0) {
			$this->_error->setErrorMessage($this->getErrorMsg('noSizes')); 
           $this->_result->append($this->_error);
           return $this->_result;
        }
		
		// Check if we need to process Local or international Shipping
	    if ($this->_dstCountryId == self::JAPAN){
			// Check if any local shipments is enable
			$_locEnable = false;
			foreach ($this->_locMethodCode as $_method) {
		        if(Mage::getStoreConfig(self::CODE.'/'.$_method.'/showmethod')) {
					$_locEnable = true;
		        }
			}
			// Exit if the are no local methods is not enable
			if (!$_locEnable) return false;
			// Process the local rate
			$this->shipLocal($request->getPackageValue());
        }

		// Check if we actually did produced Rates
		// If not inform the customer
        $rates = $this->_result->getAllRates();
        if(count($rates) == 0) {
           $this->_error->setErrorMessage($this->getErrorMsg('nodata')); 
           $this->_result->append($this->_error);
           return $this->_result;
        }

		// If we do have rates then we can return it
		return $this->_result;
	}

	/**
	* Process Local Shipping
	*
	*/
	public function shipLocal($cartPrice)
	{
		// Set the methods Scope and resource
		$resource = Mage::getResourceModel(self::CODE.'/local');

		// Get Cart Dimensions and Methods limits
		$orderDimension = $this->_finalVolumes['Vol_Basic'];
		$maxDimension = Mage::getStoreConfig(self::CODE.'/general/locmaxsize');

		// Check if the order is over size for the local Shipping
		// If it is over limit inform the customer
		if($orderDimension > $maxDimension) { 
			$this->_error->setErrorMessage($this->getErrorMsg('overSize')); 
			$this->_result->append($this->_error);
			return $this->_result;
		}
			
		// Process each Local Methods
		foreach ($this->_locMethodCode as $method_code) {
			// Skip the Method if not enabled
			if(!Mage::getStoreConfig(self::CODE.'/' .$method_code .'/showmethod')) continue;

			// Check if the method is Mail-Bin or Ta-Q-Bin
			if($method_code == 'mailbin'){
				// Validate the method
				if(!$resource->validateMailBin($this->_finalVolumes,$this->_cartWeight)) continue;
				// Get the current cart height
				$cartHeight = $this->_finalVolumes['Raw_Sizes'][0];
				// Get the method rate
				$rate = $resource->getMailRate($cartHeight);
				// Do not process if the rate is not available
				if(!$rate || $rate == 0) continue;
				// If the Express service was selected update the Shipping Quote
				if (Mage::getSingleton(self::CODE.'/session')->getShippingNeedExpress()){
					$rate += Mage::getStoreConfig(self::CODE.'/mailbin/mailexpressfee');
				}
			}else{
				// Get sourde and dextination region name in Japanese locale
				$srcProvince = $this->loadRegionByLocale($this->_srcProvinceId, 'ja_JP');
				$dstProvince = $this->loadRegionByLocale($this->_dstProvinceId, 'ja_JP');

				// Set rate calculation Data
				$data = array(
					'method_code'		=> $method_code,
					'dstProvince'		=> $dstProvince,
					'srcProvince'		=> $srcProvince,
					'orderDimension'	=> $orderDimension,
					'height'			=> $this->_finalVolumes['Raw_Sizes'][0],
					'weight'			=> $this->_cartWeight
				);

				// Get the rate for the current Cart
				$rate = $resource->getRate($data);
				// Do not process if the rate is not available
				if(!$rate || $rate == 0) continue;
				// If the cool service was selected update the Shipping Quote
				if (Mage::getSingleton(self::CODE.'/session')->getShippingNeedCool()){
					$rate += $resource->getCoolRate($this->_finalVolumes['Vol_Basic'],$this->_cartWeight);
				}
			}

			// Get the Method Title
			$method_title = Mage::getStoreConfig(self::CODE.'/'.$method_code .'/title');
			
			// Get the Service Fee
			$serviceFees = Mage::getStoreConfig(self::CODE.'/' .$method_code .'/finaladd');
			
			// Add the Service Fees to the rate
			$rate += $serviceFees;

			// Add the resulted quote to the Shipping results
			$this->addMethod($method_code,$method_title,$rate);
		}
	}

	/**
	* Get the Profit Value
	*
    * @param string $rate = Rate for percentage profit calculation
    * @return int
	*/
	public function getProfit($rate)
	{
		// Get profit calculation Parameters
		$profitType = Mage::getStoreConfig(self::CODE.'/general/profit_type');
		$profitValue = Mage::getStoreConfig(self::CODE.'/general/profit_fee');

		// check if the profit is percentage based and if we have a valid base profit value
		if ($profitValue > 0 && $profitType == 'P') {
			$profitValue = ($rate/100) * $profitValue;
		} 
		// Set to 0 negative profits
		if($profitValue < 0) $profitValue = 0;
		
		// return the profit value for the Method
		return $profitValue;
	}

	/**
	* Add the given method result to the shipping Results
	*
    * @param string $method_code = Unique code for the Shipping Method
    * @param string $method_title = Title the Shipping Method
    * @param string $rate = Rate of the Shipping Method Quote
    * @return int
	*/
	public function addMethod($method_code,$method_title,$rate)
	{
		// Get the helper
		$helper = Mage::helper(self::CODE);
		
		// Add the Profits to the Rate
		$rate += $this->getProfit($rate);
			
		// Convert the Rate if needed
		if ($this->_exchangeRequired) $rate = $helper->convertRate($rate,true);

		// Populate the Method Object
		$method = Mage::getModel('shipping/rate_result_method');
		$method->setCarrier(self::CODE);
		$method->setCarrierTitle($this->getConfigData('title'));
		$method->setMethod($method_code);
		$method->setMethodTitle($method_title);
		$method->setCost($rate);
		$method->setPrice($this->getFinalPriceWithHandlingFee($rate));
		// Append the method to the global result
		$this->_result->append($method);
	}

	/**
	* Load the Region name from the Database for the given locale
	*
    * @param int $regionId = Unique ID for the region
    * @param string $locale = Locale needed
    * @return string
	*/
	public function loadRegionByLocale($regionId, $locale)
	{
		// Connectio Parameters
		$region = Mage::getModel('directory/region');
		$resource = Mage::getSingleton('core/resource');
        $regionTable = $resource->getTableName('directory/country_region');
        $regionNameTable = $resource->getTableName('directory/country_region_name');
        $read = $resource->getConnection('directory_read');

		// Database Query
        $select = $read->select()
            ->from(array('region'=>$regionTable))
            ->where('region.region_id=?', $regionId)
            ->join(array('rname'=>$regionNameTable),
                'rname.region_id=region.region_id AND rname.locale=\''.$locale.'\'',
                array('name'));

		// Set the query result in the region model
        $region->setData($read->fetchRow($select));
		// return the region name in the requested locale
		return $region->getName();
	}

    /**
     * Get the Error Message
     *
     * @param string $type = type of error requested
     * @param (optional) array $val = additonal parameters for the Error message
     * @return Error Message Object
     */
    public function getErrorMsg($type,$val = array(0,0))
    {
		
        $error = array(
		'overWeight'			=> Mage::helper(self::CODE)->__("The weight of your order exceeds the limit for this shipping service. Please feel free to contact us for see if an alternative shipping service is available or please divide your order in 2 or more orders and proceed to checkout."),
		'noProvince'			=> Mage::helper(self::CODE)->__("Please select a valid State/Province."),
		'overSize'				=> Mage::helper(self::CODE)->__("The dimensions of your order exceeds the limit for this shipping service. Please feel free to contact us for see if an alternative shipping service is available or please divide your order in 2 or more orders and proceed to checkout."),
		'unsupportedCountry'	=> Mage::helper(self::CODE)->__("This shipping service is not available for the selected Country. Please feel free to contact us for see if an alternative shipping service is available"),
		'countryWeightLimit'	=> Mage::helper(self::CODE)->__("The weight of your order exceeds the weight limit for the selected country. The weight of your order is: %d(g), while the weight limit for the selected country is: %d(g). Please feel free to contact us for see if an alternative shipping service is available or please divide your order in 2 or more orders and proceed to checkout.", $val[0], $val[1]),
		'nodata'	=> Mage::helper(self::CODE)->__("The Shipping method cannot be processed. Please contact us for information on alternative form of shipping"),
		'noSizes'	=> Mage::helper(self::CODE)->__("The Shipping method cannot be processed. Valid dimensions for the products were not found"),
		'noExchange'	=> Mage::helper(self::CODE)->__("The Shipping method cannot be processed. An exchange rate from %s to %s is not available.", $val[0], $val[1]),
		);
		return $error[$type];
	}

    /**
     * Get all Allowed methods for the Carrier
     * This method is needed for all Core Module and 3rd Part Modules (such as GoogleChackout)
     * which needs to integrate with the Carrier
     *
     * TODO:
     * 1) See if it would be better to Pass the Title or a more readable label rather then the code
     * 2) Are the quotes getted correctly from 3rd Parts?
     *
     * @return array = all available Shipping Method for the Carrier
     */
    public function getAllowedMethods()
    {
		// Global Methods Array
		$carMethods = array (
				'mailbin'			=> Mage::helper(self::CODE)->__('Mail-BIN'),
				'taqbin'			=> Mage::helper(self::CODE)->__('Ta-Q-BIN'),
		);
		
		// Allowed Methods Array
		$allmethods = array();
		// Add to the allow methods array all methods which are enable
		foreach ($carMethods as $methodCode=>$methodTitle) {
			if(Mage::getStoreConfig(self::CODE.'/'.$methodCode.'/showmethod')) {
				$allmethods[$methodCode] = $methodTitle;
			}
		}
		// Return the Methods Array
		return $allmethods;
    }

    /**
     * Check if carrier has shipping tracking option available
     *
     * @return boolean
     */
    public function isTrackingAvailable()
    {
        return true;
    }

    /**
     * Check if carrier has shipping label option available
     * TODO: Need To create Shipping Label management
     * @return boolean
     */
    public function isShippingLabelsAvailable()
    {
        return true;
    }

    /**
     * Get Tracking info
     *
     * @param mixed $trackings
     * @return mixed
     */
    public function getTrackingInfo($tracking)
    {
        // Define returning array
        $info = array();

        // Get tracking results
        $result = $this->getTracking($tracking);

        // Validate result and return it acording to the format
        if($result instanceof Mage_Shipping_Model_Tracking_Result){
            if ($trackings = $result->getAllTrackings()) {
                return $trackings[0];
            }
        }
        elseif (is_string($result) && !empty($result)) {
            return $result;
        }

        // Return null if no results are available
        return false;
    }

    /**
     * Get tracking
     *
     * @param mixed $trackings
     * @return mixed
     */
	public function getTracking($trackings)
    {
        // Define returning array
		$return = array();

		// Validate given parameter
        if (!is_array($trackings)) {
            $trackings = array($trackings);
        }

		//Set tracking model
        $result = Mage::getModel('shipping/tracking_result');
        $defaults = $this->getDefaults();

		// Get Terget language acording to user browser language
		$lang = $this->getTargetLang();
		// Format Tracking Link
		$trackUrlPre = "<a href=".self::TRACK_URL .'?requestNo1=';
		$trackUrlPost = " target='_blank'>".Mage::helper(self::CODE)->__('Click Here to Track your shipment')."</a>";
		
		// Process each tracking and update the carrier information
        foreach($trackings as $trackingNum){
			$trackingURL = $trackUrlPre.$trackingNum.$trackUrlPost;
			if (version_compare(Mage::getVersion(), '1.7.0', '>=')) $trackingURL = self::TRACK_URL .'?requestNo1='.$trackingNum;;
			// Force base english link to all non Japanese browser
			if ($lang !='ja') $trackingURL = 'http://track.kuronekoyamato.co.jp/english/tracking';
            $status = Mage::getModel('shipping/tracking_result_status');
            $status->setCarrier('yamato');
            $status->setCarrierTitle($this->getConfigData('title'));
            $status->setTracking($trackingNum);
            $status->setPopup(1);
            $status->setUrl($trackingURL);

            // append tracking status
			$result->append($status);
        }
        // set result
        $this->_result = $result;
        // return result
        return $this->_result;
    }

    /**
     * Get the Target Language according to user available languages
     *
     * @return array
     */
	function getTargetLang()
	{
		// Parse all user available Languages
	    if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
	        foreach (explode(",", strtolower($_SERVER['HTTP_ACCEPT_LANGUAGE'])) as $accept) {
	            if (preg_match("!([a-z-]+)(;q=([0-9.]+))?!", trim($accept), $found)) {
	                $langs[] = $found[1];
	                $quality[] = (isset($found[3]) ? (float) $found[3] : 1.0);
	            }
	        }
	    }
		// Get the first User Language compatible with the target site
        foreach ($langs as $lang) {
			if (in_array($lang, $this->_langUrl)) return $lang;
        }
		// return the first default Language
	    return $this->_langUrl[0];
	}
}