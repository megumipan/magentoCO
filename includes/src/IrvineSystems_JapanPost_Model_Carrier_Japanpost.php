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

class IrvineSystems_JapanPost_Model_Carrier_Japanpost extends Mage_Shipping_Model_Carrier_Abstract
{
	// Model Constants
    const JAPAN = 'JP'; // Japan Country Code
    const PRICECUR = 'JPY'; // Price List Currency Code
    const CODE = 'japanpost'; // Carrier Code

    // Carrier Tracking Constants
	const TRACK_URL = 'https://trackings.post.japanpost.jp/services/srv/search/direct?searchKind=S004';
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
	protected $_locMethodCode = array ('yuupack','teikei','teikeigai');
	
	// Supported Methods Array
	protected $_intMethodCodes = array ('ems','airmail','sal','surface');
	
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
		// Check if the Carrier is enable
		if (!Mage::getStoreConfig('carriers/'.self::CODE.'/active')) return false;

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
		// before proceed let's be sure the prefecture is selected for Japan and it is valid
        if($this->_dstCountryId == self::JAPAN && !$this->_dstProvinceId){
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
		switch($this->_dstCountryId){
		    case (self::JAPAN):
				// Exit if the are no local methods is not enable
				$_locEnable = false;
				foreach ($this->_locMethodCode as $_method) {
			        if(Mage::getStoreConfig(self::CODE.'/'.$_method.'/showmethod')) {
						$_locEnable = true;
			        }
				}
				if (!$_locEnable) return false;
				$this->shipLocal();
		    break;
			default:
				// Exit if there are no international methods enable
				$_intEnable = false;
				foreach ($this->_intMethodCodes as $_method) {
			        if(Mage::getStoreConfig(self::CODE.'/'.$_method.'/showmethod')) {
						$_intEnable = true;
			        }
				}
				if (!$_intEnable) return false;
				$this->shipInternational();
		    break;
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
	* Process International Shipping
	*
	*/
	public function shipInternational()
	{
		// Set the methods Scope and resource
		$scope = 'international';
		$resource = Mage::getResourceModel(self::CODE.'/'.$scope);

		// Get Cart Dimensions and Methods limits
		$Pack_Length = $this->_finalVolumes['Vol_Length'];
		$Pack_Volume = $this->_finalVolumes['Vol_Volume'];
		$intmaxlength = Mage::getStoreConfig(self::CODE.'/general/intmaxlength');
		$intmaxvolume = Mage::getStoreConfig(self::CODE.'/general/intmaxvolume');

		// Now the dimension limit are a bit variable from country to country.
		// Generally are divide in two:
		// 1. Max Length: 1500mm and max volum (lenght+circum.) of 3000mm
		// 1. Max Length: 1050mm and max volum (lenght+circum.) of 2000mm
		// 90% of the cases use the first option therefore we use those limit as standard.
		// I will sligtly increase the limit, this because even if the consolidation of the
		// oreder is pretty accurate, there may be additional space on big orders. therefore
		// we will add some margin. 
		// More information here:
		// - http://www.post.japanpost.jp/int/ems/size/index_en.html 
		// If the order is too big inform the customer
        if($Pack_Length > $intmaxlength || $Pack_Volume > $intmaxvolume) { 
           $this->_error->setErrorMessage($this->getErrorMsg('overSize')); 
           $this->_result->append($this->_error);
           return $this->_result;
        }

		// Now we load the data for our destination country
		// The index for the result is the following:
		// [0] = Country Weight Limit
		// [1] = 0 to 10Kg Tier, EMS Price zone (if not available value = 'NONE')
		// [2] = 0 to 10Kg Tier, Airmail Price zone (if not available value = 'NONE')
		// [3] = 0 to 10Kg Tier, SAL Price zone (if not available value = 'NONE')
		// [4] = 0 to 10Kg Tier, Surface Mail Price zone (if not available value = 'NONE')
		// [5] = 10 to 20Kg Tier, EMS Price zone (if not available value = 'NONE')
		// [6] = 10 to 20Kg Tier, Airmail Price zone (if not available value = 'NONE')
		// [7] = 10 to 20Kg Tier, SAL Price zone (if not available value = 'NONE')
		// [8] = 10 to 20Kg Tier, Surface Mail Price zone (if not available value = 'NONE')
		// [9] = 20 to 30Kg Tier, EMS Price zone (if not available value = 'NONE')
		// [10] = 20 to 30Kg Tier, Airmail Price zone (if not available value = 'NONE')
		// [11] = 20 to 30Kg Tier, SAL Price zone (if not available value = 'NONE')
		// [12] = 20 to 30Kg Tier, Surface Mail Price zone (if not available value = 'NONE')
        $countryInfo = $resource->CountryData($this->_dstCountryId);

		// There is very few Countries which are not supported, let's check if the country is supported..
		// If it is not supported inform the customer
        if(!$countryInfo[0]) {
           $this->_error->setErrorMessage($this->getErrorMsg('unsupportedCountry')); 
           $this->_result->append($this->_error);
           return $this->_result;
        }

		// Some countries have a weight limitation below the carrier limit.
		// first let's check if our country overpass that limit.
		// If it is over limit inform the customer with the specific country information
        if($this->_cartWeight > $countryInfo[0]) {
           $this->_error->setErrorMessage($this->getErrorMsg('countryWeightLimit', array($this->_cartWeight, $countryInfo[0]))); 
           $this->_result->append($this->_error);
           return $this->_result;
        }

		// Get the weight limit
		$maxWeight = Mage::getStoreConfig(self::CODE.'/general/max_package_weight');
		// Get the methods zone IDs based on Cart weight
		$zoneIds = $resource->_getZoneIds($this->_cartWeight,$maxWeight);

		// Process each International Methods
		foreach ($this->_intMethodCodes as $method_code) {
				
			// proceed if the current method is enable
			if (Mage::getStoreConfig(self::CODE.'/' .$method_code .'/showmethod')) {
                // Get Shipping Method Title
				$method_title = Mage::getStoreConfig(self::CODE.'/' .$method_code .'/title');

                // Get Country specific ZoneID for the current Method
				$zoneId = $zoneIds[$method_code];

				// Get the correct Zone for the current destination country
				switch($countryInfo[$zoneId]){
				    case ('Zone 1'): // Common for EMS and other International methods
						$zone = 0;
				    break;
				    case ('Zone 2'): // Only for other International methods
						$zone = 1;
				    break;
				    case ('Zone 2-1'): // Only for  EMS
						$zone = 1;
				    break;
				    case ('Zone 2-2'): // Only for  EMS
						$zone = 2;
				    break;
				    case ('Zone 3'): // Common for EMS and other International methods
						// Check if we are using EMS or another method.
						// Since Ems divide the shipping zone differently from the other methods
						if ($method_code == 'ems'){
							$zone = 3;
						}else{
							$zone = 2;
						}
				    break;
				    case ('Zone 4'): // Only for other International methods
						$zone = 3;
				    break;
				    case ('NONE'): // Invalidation Case
						$zone = 4;
				    break;
				}
					
				// even if is enable, a specific method may be not available for the given Weight Range,
				// if is available we proceed.
				// if one method is not available we will not stop the whole for each,
				// if all method will be invalid the error will already be handle before
				// returning the results object to the shipping Module. So we can proceeed safely
				if ($zone < 4) {

					// Get the base Rate
					$baseRate = $resource->getRate($this->_cartWeight,$method_code,$zone);
					// Get the Service Fee
					$serviceFees = $this->getServiceFees($method_code);
						
					// For all Non-EMS shipping method we have a special price on small orders.
					// we need to check 3 factors for see if it is eligible, 1.weight below 2kg,
					// 2. lenght below 60cm and 3. overhaul size (l+w+h) below 90cm.
					// Let's if this order is eligible for our special shipping price.
					$this->_special = false;
					if ($method_code != 'ems') $this->_special = $this->checkSpecial();
					
					// Set the rate value for the current Method
					$rate = $baseRate + $serviceFees;	
						
					// before adding the final price let's see if a Special Price is available
			        if($this->_special) { 
						// for the bonus class the Zones 2 and 3 are grouped toghere and Zone 4 is moved one step backward.
						// therefore let's fix the indexes if necessary, by moving zone 3 and 4 one step backward.
						if ($zone > 2)$zone -= 1;
						// In the case of Surface Shipping, instead, there will be only Zone 0
						if ($method_code == 'surface')$zone = 0;

						// now we can get the Rate
						$bonusRate = $resource->getBonusRate($this->_cartWeight,$method_code,$zone);
								
						// Extimate a percentage discount base from the original Rate to the Bonus Rate
						$oriRate = $rate;	
						$rate = $bonusRate + $serviceFees;	
						$a = $rate/$oriRate;
						$a = round($a*100);
						$disc = 100-$a;
						// Check if Magento version is above to 1.6.
						// If it is we cannot use HTML on the Title (unless we modify a long list of files)
						if(Mage::getVersion() < 1.7){
							// Update the shipping method with the discount information
							$method_title = $method_title .' <span style="color: #ff0000;"><em><strong>' .$disc .'% Off!!</strong></em></span>';
						}else{
							$method_title = $method_title.' (' .$disc .'% Off!!)';;
						}
					}

					// Add the resulted quote to the Shipping results
					$this->addMethod($method_code,$method_title,$rate);
				}
			}
		}
	}

	/**
	* Process Local Shipping
	*
	*/
	public function shipLocal()
	{
		// Set the methods Scope and resource
		$scope = 'local';
		$resource = Mage::getResourceModel(self::CODE.'/'.$scope);

		// Process each Local Methods
		foreach ($this->_locMethodCode as $method_code) {
			
			// Skip the Method if not enabled
			if(!Mage::getStoreConfig(self::CODE.'/' .$method_code .'/showmethod')) continue;

			// Validate if teikei can be processed
			if($method_code == 'teikei' &&
				!$resource->validateTeikei($this->_cartWeight,$this->_finalVolumes['Raw_Sizes'])) continue;

			// Validate if teikei can be processed
			if($method_code == 'teikeigai' &&
				!$resource->validateTeikeiGai($this->_cartWeight,$this->_finalVolumes['Raw_Sizes'])) continue;

			// Validate if yuupack can be processed
			if(($method_code == 'yuupack' || $method_code == 'yuupackcod') &&
				!$resource->validateYuuPack($this->_finalVolumes['Vol_Basic'])) continue;

			// Get the Method Title
			$method_title = Mage::getStoreConfig(self::CODE.'/'.$method_code .'/title');

			// Get the Service Fee
			$serviceFees = $this->getServiceFees($method_code,true);
			
			// Get sourde and dextination region name in Japanese locale
			$srcProvince = $this->loadRegionByLocale($this->_srcProvinceId, 'ja_JP');
			$dstProvince = $this->loadRegionByLocale($this->_dstProvinceId, 'ja_JP');

			// Set rate calculation Data
			$data = array(
				'method_code'	=> $method_code,
				'dstProvince'	=> $dstProvince,
				'srcProvince'	=> $srcProvince,
				'orderDimension'=> $this->_finalVolumes['Vol_Basic'],
				'weight'		=> $this->_cartWeight
				);

			// Get the rate for the current Cart
			$rate = $resource->getRate($data);

			// Do not process if the rate is not available
			if(!$rate || $rate == 0) continue;

			// If the cool service was selected update the Shipping Quote
			if (Mage::getSingleton(self::CODE.'/session')->getShippingNeedCool()){
				$rate += $resource->_getCoolRate();
			}

			// Add the Service Fees to the rate
			$rate += $serviceFees;
			
			// Add the resulted quote to the Shipping results
			$this->addMethod($method_code,$method_title,$rate);
		}
	}

	/**
	* Get the service Fees for the given Method
	* Service Fees includes:
	* - Additional Method Costs (if Any)
	* - Additional Ensurence Cost (if Any)
	*
    * @param string $method_code	= Unique code for the Shipping Method
    * @param string $local 			= Flag for local shipping (optional)
    * @return int
	*/
	public function getServiceFees($method_code, $local = null)
	{
		// Get final Addition Fees
		$finalAdd = Mage::getStoreConfig(self::CODE.'/' .$method_code .'/finaladd');
		// Add the Final add to the Service fees
		$ServiceFee = $finalAdd;

		// if we are using a local shipping, there is no ensurance,
		// therefore simply return the Final Addition Fees (if any)
		if ($local) return $ServiceFee;
		
		/** Calculate the Ensurance Fees **/
		// Get cart subtotal
		$subtotal = Mage::getModel('checkout/session')->getQuote()->getBaseSubtotal();
		// Get fee parameters
		$baseAdd = Mage::getStoreConfig(self::CODE.'/' .$method_code .'/baseadd');		// Base Ensurance Cost
		$basevalue = Mage::getStoreConfig(self::CODE.'/' .$method_code .'/basevalue');	// Base Order Amount
		$tieradd = Mage::getStoreConfig(self::CODE.'/' .$method_code .'/tieradd');		// Additional Ensurance Cost
		$tiervalue = Mage::getStoreConfig(self::CODE.'/' .$method_code .'/tiervalue');	// Multiplicator Tier Amount

		// Add the base ensurance cost
		// this is always applyed regardless to the cart value
		$ServiceFee += $baseAdd;

		// Now we check if the Cart Value needs additional Ensurance Cost
		// We check also if there is a positive tier value,
		// if the tear value is set to 0 then additional ensurance cost cannot be calculated
		if ($subtotal > $basevalue && $tiervalue > 0) {
			// if the subtotal is above, we need to multipli the 'tiervalue' times how
			// many times the subtotal exced the 'tiervalue'

			// Get the amount in which the subtotal exeed the BaseValue
			$exVal = $subtotal-$basevalue;

			// Get the number of tier for the amount in excess
			$enTiers = floor($exVal/$tiervalue);
			
			// Check if the Difference reaches an additional level otherwise we dont nee to add additional Ensurance Cost
			if ($enTiers>0)	{		
				// Multiply the Additional Ensurance Cost for the number of Ensurance Tiers
				$enAdd = $tieradd*$enTiers;
				// Add the Additional Ensurance to the Service Fees
				$ServiceFee += $enAdd;
			}
		} 
		// Return the Service fees
		return $ServiceFee;
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
		// Add the Profits to the Rate
		$rate += $this->getProfit($rate);
			
		// Convert the Rate if needed
		if ($this->_exchangeRequired) $rate = Mage::helper(self::CODE)->convertRate($rate,true);

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
	* Check if the Current Order is eligible for Special Price
	* 
    * @return bool
	*/
	public function checkSpecial()
	{
		// Check if the Special Prices are enable
		if(Mage::getStoreConfig(self::CODE.'/special/showspecial')) {
			// Get Special Price Calculation Parameters
			$Vol_Basic = $this->_finalVolumes['Vol_Basic'];
			$Vol_Length = $this->_finalVolumes['Vol_Length'];
			$weight_limit = Mage::getStoreConfig(self::CODE.'/special/weight_limit');
			$size_limit = Mage::getStoreConfig(self::CODE.'/special/size_limit');
			$length_limit = Mage::getStoreConfig(self::CODE.'/special/length_limit');

			// Check if the dimension are eligible
	        if($this->_cartWeight <= $weight_limit) { 
		        if($Vol_Length <= $length_limit && $Vol_Basic <= $size_limit) { 
					// return true if the order is eligible and the Special Prices are enable
					return true;
		        }
	        }
        }
		// return false if the order is not eligible on the Special Prices are not enable
		return false;
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
     * @return array = all available Shipping Method for the Carrier
     */
    public function getAllowedMethods()
    {
		// Global Methods Array
		$carMethods = array (
			'yuupack'		=> Mage::helper(self::CODE)->__('YuuPack'),
			'teikei'		=> Mage::helper(self::CODE)->__('Teikei'),
			'teikeigai'		=> Mage::helper(self::CODE)->__('TeikeiGai'),
			'ems'			=> Mage::helper(self::CODE)->__('EMS'),
			'airmail'		=> Mage::helper(self::CODE)->__('Airmail'),
			'sal'			=> Mage::helper(self::CODE)->__('SAL'),
			'surface'		=> Mage::helper(self::CODE)->__('Surface')
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
		$trackUrlPre = "<a href=".self::TRACK_URL .'&locale='.$lang.'&reqCodeNo1=';
		$trackUrlPost = " target='_blank'>".Mage::helper(self::CODE)->__('Click Here to Track your shipment')."</a>";
		
		// Process each tracking and update the carrier information
        foreach($trackings as $trackingNum){
			$trackingURL = $trackUrlPre.$trackingNum.$trackUrlPost;
			if (version_compare(Mage::getVersion(), '1.7.0', '>=')) $trackingURL = self::TRACK_URL .'&locale='.$lang.'&reqCodeNo1='.$trackingNum;;
            $status = Mage::getModel('shipping/tracking_result_status');
            $status->setCarrier('japanpost');
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