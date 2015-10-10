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

class IrvineSystems_Sagawa_Model_Slips extends Mage_Rule_Model_Rule
{
    // Bool Types Constants
    const BOOL_FALSE				= 0;
    const BOOL_TRUE					= 1;

    // Packages Code Types Constants
    const PKG_CODE_BOX				= '001';
    const PKG_CODE_BAGS				= '002';
    const PKG_CODE_CASE				= '003';
    const PKG_CODE_ENV				= '004';
    const PKG_CODE_GOLF				= '005';
    const PKG_CODE_SKI				= '006';
    const PKG_CODE_BOARD			= '007';
    const PKG_CODE_OTHER			= '008';

    // Shipping Methods Code Types Constants
    const SHP_METHOD_EXP			= '000';
    const SHP_METHOD_SUPEREXP		= '001';
    const SHP_METHOD_SOKUHAIEXP		= '002';
    const SHP_METHOD_AIRDAY			= '003';
    const SHP_METHOD_AIRMORNING		= '004';
    const SHP_METHOD_TIMEEXP		= '005';
    const SHP_METHOD_MAIL			= '999';
    
    // Cooling Shipment Types Constants
    const COOL_SHIP_NONE			= '001';
    const COOL_SHIP_COOL			= '002';
    const COOL_SHIP_FROZE			= '003';

    // Shipping Time Zone Classes Constants
    const TIME_ZONE_5				= 0;
    const TIME_ZONE_6				= 1;

    // Shipping Time Zone 5 Constants
    const TIME_ZOME_5_1ST			= '01';
    const TIME_ZOME_5_2ND			= '12';
    const TIME_ZOME_5_3RD			= '14';
    const TIME_ZOME_5_4TH			= '16';
    const TIME_ZOME_5_5TH			= '04';

    // Shipping Time Zone 6 Constants
    const TIME_ZOME_6_1ST			= '01';
    const TIME_ZOME_6_2ND			= '12';
    const TIME_ZOME_6_3RD			= '14';
    const TIME_ZOME_6_4TH			= '16';
    const TIME_ZOME_6_5TH			= '18';
    const TIME_ZOME_6_6TH			= '19';

    // Cash on delivery Payment Method Constants
    const COD_PAY_NONE				= 0;
    const COD_PAY_ANY				= 1;
    const COD_PAY_CASH				= 2;
    const COD_PAY_CARD				= 5;

    // Ensured Printed Constants
    const ENSURED_NOPRINT			= 0;
    const ENSURED_PRINT				= 1;

    // Services Codes Constants
    const SRV_CODE_COOL				= '001';
    const SRV_CODE_FROZE			= '002';
    const SRV_CODE_SUPEREXP			= '003';
    const SRV_CODE_PICKUP			= '004';
    const SRV_CODE_DELDAY			= '005';
    const SRV_CODE_DELTIME5			= '007';
    const SRV_CODE_CODCASH			= '008';
    const SRV_CODE_CODCARD			= '009';
    const SRV_CODE_CODANY			= '010';
    const SRV_CODE_CARE				= '011';
    const SRV_CODE_VALUE			= '012';
    const SRV_CODE_SIDE				= '013';
    const SRV_CODE_SOKUHAI			= '014';
    const SRV_CODE_DELTIMEMORNING	= '016';
    const SRV_CODE_AIR				= '017';
    const SRV_CODE_TIMEEXP			= '018';
    const SRV_CODE_DELTIME6			= '019';

    // Delivery Types Constants
    const DEV_NORMAL				= 0;
    const DEV_PICKUP				= 1;

    // SRC Classification Constants
    const SRC_NONE					= 0;
    const SRC_SRC					= 1;

    // Delivery Payments Source Types Constants
    const DEL_PAY_PRE				= 1;
    const DEL_PAY_COD				= 2;

    /**
     * Model Constructor
     * 
     */
    public function _construct()
    {
        // Construct parent
        parent::_construct();
        // Initialize cartrules collection
        $this->_init('sagawa/slips');
    }

   /**
    * Public getter for Bool Types Array
    *
    * @return array
    */
    public function getBoolTypes()
    {
        return array(
            self::BOOL_FALSE		=> Mage::helper('sagawa')->__('FALSE'),
            self::BOOL_TRUE			=> Mage::helper('sagawa')->__('TRUE'),
        );
    }

   /**
    * Public getter for Packages Code Types Array
    *
    * @return array
    */
    public function getPkgCodeTypes()
    {
        return array(
            self::PKG_CODE_BOX		=> Mage::helper('sagawa')->__('Box'),
            self::PKG_CODE_BAGS		=> Mage::helper('sagawa')->__('Bags'),
            self::PKG_CODE_CASE		=> Mage::helper('sagawa')->__('Suitcase'),
            self::PKG_CODE_ENV		=> Mage::helper('sagawa')->__('Envelope Type'),
            self::PKG_CODE_GOLF		=> Mage::helper('sagawa')->__('Golf Bag'),
            self::PKG_CODE_SKI		=> Mage::helper('sagawa')->__('Ski'),
            self::PKG_CODE_BOARD	=> Mage::helper('sagawa')->__('Snowboard'),
            self::PKG_CODE_OTHER	=> Mage::helper('sagawa')->__('Other'),
        );
    }

   /**
    * Public getter for Shipping Methods Code Types Array
    *
    * @return array
    */
    public function getShpMethodCodeTypes()
    {
        return array(
            self::SHP_METHOD_EXP			=> Mage::helper('sagawa')->__('Hikyaku Express'),
            self::SHP_METHOD_SUPEREXP		=> Mage::helper('sagawa')->__('Hikyaku Super Express'),
            self::SHP_METHOD_SOKUHAIEXP		=> Mage::helper('sagawa')->__('Hikyaku Sokuhai Express'),
            self::SHP_METHOD_AIRDAY			=> Mage::helper('sagawa')->__('Hikyaku Air Express (next day)'),
            self::SHP_METHOD_AIRMORNING		=> Mage::helper('sagawa')->__('Hikyaku Air Express (next morning)'),
            self::SHP_METHOD_TIMEEXP		=> Mage::helper('sagawa')->__('Hikyaku Just Time Express'),
            self::SHP_METHOD_MAIL			=> Mage::helper('sagawa')->__('Hikyaku Mail'),
        );
    }

   /**
    * Public getter for Cooling Shipment Types Array
    *
    * @return array
    */
    public function getCoolShipCodeTypes()
    {
        return array(
            self::COOL_SHIP_NONE	=> Mage::helper('sagawa')->__('None'),
            self::COOL_SHIP_COOL	=> Mage::helper('sagawa')->__('Cool'),
            self::COOL_SHIP_FROZE	=> Mage::helper('sagawa')->__('Frozen'),
        );
    }

   /**
    * Public getter for Time Zone Classes Types Array
    *
    * @return array
    */
    public function getTimeZoneClassesTypes()
    {
        return array(
            self::TIME_ZONE_5	=> Mage::helper('sagawa')->__('Class 5 Zone'),
            self::TIME_ZONE_6	=> Mage::helper('sagawa')->__('Class 6 Zone'),
        );
    }

    /**
     * Public getter for Currently Selected Time Zone Indexes
     *
     * @return array
     */
    public function getTimesZoneIndexesArray()
    {
    	// Get the configured time class
    	$timeClass = Mage::getStoreConfig('sagawa/slips/ship_time_class');
    	
    	// Get the time class options
    	if ($timeClass == self::TIME_ZONE_5) return $this->getTimeZoneShortIndexes();

    	return $this->getTimeZoneLongIndexes();
    }
    
    /**
     * Public getter for Currently Selected Time Zone Array
     *
     * @return array
     */
    public function getTimesZoneArray()
    {
    	// Get the configured time class
    	$timeClass = Mage::getStoreConfig('sagawa/slips/ship_time_class');
    	
    	// Get the time class options
    	if ($timeClass == self::TIME_ZONE_5) return $this->getTimeZoneShortTypes();

    	// Return the result
    	return $this->getTimeZoneLongTypes();
    }
    
   /**
    * Public getter for Currently Selected Times
    *
    * @return array
    */
    public function getTimesArray()
    {
		$returnValues = $this->getTimesZoneArray();
    	return $returnValues;
    }

    /**
     * Public convert a time index in its equavelent String
     * Return null if the index is not found
     * 
     * @param index [String]
     * 
     * @return String
     */
    public function timeIndexToString($index)
    {
    	// Get All Available Time
    	//$times = $this->getTimesZoneIndexesArray();
    	$times = $this->getTimeZoneFullTypes();
    	
    	// Chek if an there is a match for the index
    	if (in_array($index, $times)) {
    		// Return the string for the Time zone array
   			return $times[$index];
    	}

    	// Return null if nothing is found
    	return null;
    }
    
    /**
    * Public getter for Time Zone 5 Types Array
    *
    * @return array
    */
    public function getTimeZoneShortTypes()
    {
        return array(
            self::TIME_ZOME_5_1ST	=> Mage::helper('sagawa')->__('Morning'),
            self::TIME_ZOME_5_2ND	=> Mage::helper('sagawa')->__('12:00～14:00'),
            self::TIME_ZOME_5_3RD	=> Mage::helper('sagawa')->__('14:00～16:00'),
            self::TIME_ZOME_5_4TH	=> Mage::helper('sagawa')->__('16:00～18:00'),
            self::TIME_ZOME_5_5TH	=> Mage::helper('sagawa')->__('18:00～21:00'),
        );
    }

    /**
     * Public getter for Time Zone Indexes Array
     *
     * @return array
     */
    public function getTimeZoneShortIndexes()
    {
    	return array(
    			self::TIME_ZOME_5_1ST,
    			self::TIME_ZOME_5_2ND,
    			self::TIME_ZOME_5_3RD,
    			self::TIME_ZOME_5_4TH,
    			self::TIME_ZOME_5_5TH,
    	);
    }
    
   /**
    * Public getter for Time Zone 6 Types Array
    *
    * @return array
    */
    public function getTimeZoneLongTypes()
    {
        return array(
            self::TIME_ZOME_6_1ST	=> Mage::helper('sagawa')->__('Morning'),
            self::TIME_ZOME_6_2ND	=> Mage::helper('sagawa')->__('12:00～14:00'),
            self::TIME_ZOME_6_3RD	=> Mage::helper('sagawa')->__('14:00～16:00'),
            self::TIME_ZOME_6_4TH	=> Mage::helper('sagawa')->__('16:00～18:00'),
            self::TIME_ZOME_6_5TH	=> Mage::helper('sagawa')->__('18:00～20:00'),
            self::TIME_ZOME_6_6TH	=> Mage::helper('sagawa')->__('19:00～21:00'),
        );
    }
    
   /**
    * Public getter for Time Zone All types
    *
    * @return array
    */
    public function getTimeZoneFullTypes()
    {
        return array(
            self::TIME_ZOME_5_1ST	=> Mage::helper('sagawa')->__('Morning'),
            self::TIME_ZOME_5_5TH	=> Mage::helper('sagawa')->__('18:00～21:00'),
            self::TIME_ZOME_6_2ND	=> Mage::helper('sagawa')->__('12:00～14:00'),
            self::TIME_ZOME_6_3RD	=> Mage::helper('sagawa')->__('14:00～16:00'),
            self::TIME_ZOME_6_4TH	=> Mage::helper('sagawa')->__('16:00～18:00'),
            self::TIME_ZOME_6_5TH	=> Mage::helper('sagawa')->__('18:00～20:00'),
            self::TIME_ZOME_6_6TH	=> Mage::helper('sagawa')->__('19:00～21:00'),
        );
    }
	
    /**
     * Public getter for Time Zone Indexes Array
     *
     * @return array
     */
    public function getTimeZoneLongIndexes()
    {
    	return array(
    			self::TIME_ZOME_6_1ST,
    			self::TIME_ZOME_6_2ND,
    			self::TIME_ZOME_6_3RD,
    			self::TIME_ZOME_6_4TH,
    			self::TIME_ZOME_6_5TH,
    			self::TIME_ZOME_6_6TH,
    	);
    }
    
   /**
    * Public getter for Casho on Delivery Payment methods Array
    *
    * @return array
    */
    public function getCodPaymentMethodTypes()
    {
        return array(
            self::COD_PAY_NONE	=> Mage::helper('sagawa')->__('None'),
            self::COD_PAY_ANY	=> Mage::helper('sagawa')->__('Any Possible'),
            self::COD_PAY_CASH	=> Mage::helper('sagawa')->__('Cash only'),
            self::COD_PAY_CARD	=> Mage::helper('sagawa')->__('Debit or Credit Card'),
        );
    }

   /**
    * Public getter for Ensured Amount Printed Array
    *
    * @return array
    */
    public function getEnsuredPrintTypes()
    {
        return array(
            self::ENSURED_NOPRINT	=> Mage::helper('sagawa')->__('Not Printed'),
            self::ENSURED_PRINT		=> Mage::helper('sagawa')->__('Printed'),
        );
    }

   /**
    * Public getter for Services Codes Array
    *
    * @return array
    */
    public function getServiceCodesTypes()
    {
        return array(
            self::SRV_CODE_COOL				=> Mage::helper('sagawa')->__('Hikyaku Cool Express (Cool)'),
            self::SRV_CODE_FROZE			=> Mage::helper('sagawa')->__('Hikyaku Cool Express (Frozen)'),
            self::SRV_CODE_SUPEREXP			=> Mage::helper('sagawa')->__('Hikyaku Super Express'),
            self::SRV_CODE_PICKUP			=> Mage::helper('sagawa')->__('Received at Sagawa office'),
            self::SRV_CODE_DELDAY			=> Mage::helper('sagawa')->__('Delivery Day Selection'),
            self::SRV_CODE_DELTIME5			=> Mage::helper('sagawa')->__('Delivery Time Selection (Class 5)'),
            self::SRV_CODE_CODCASH			=> Mage::helper('sagawa')->__('e-collect (Cash payment)'),
            self::SRV_CODE_CODCARD			=> Mage::helper('sagawa')->__('e-collect (debit/credit card)'),
            self::SRV_CODE_CODANY			=> Mage::helper('sagawa')->__('e-collect (any payment method)'),
            self::SRV_CODE_CARE				=> Mage::helper('sagawa')->__('Handle with care'),
            self::SRV_CODE_VALUE			=> Mage::helper('sagawa')->__('Valuables'),
            self::SRV_CODE_SIDE				=> Mage::helper('sagawa')->__('Up Side Specified'),
            self::SRV_CODE_SOKUHAI			=> Mage::helper('sagawa')->__('Hikyaku Sokuhai Express'),
            self::SRV_CODE_DELTIMEMORNING	=> Mage::helper('sagawa')->__('Delivery Time-Zone Selection (Morning)'),
            self::SRV_CODE_AIR				=> Mage::helper('sagawa')->__('Hikyaku Air Express'),
            self::SRV_CODE_TIMEEXP			=> Mage::helper('sagawa')->__('Hikyaku Just Time Express'),
            self::SRV_CODE_DELTIME6			=> Mage::helper('sagawa')->__('Delivery Time-Zone Selection (Class 6)'),
        );
    }

   /**
    * Public getter for Delivery Types Array
    *
    * @return array
    */
    public function getDeliveryTypes()
    {
        return array(
            self::DEV_NORMAL	=> Mage::helper('sagawa')->__('Normal Delivery'),
            self::DEV_PICKUP	=> Mage::helper('sagawa')->__('Pickup at Branch'),
        );
    }

   /**
    * Public getter for SRC Classification Types Array
    *
    * @return array
    */
    public function getSrcClassTypes()
    {
        return array(
            self::SRC_NONE		=> Mage::helper('sagawa')->__('None'),
            self::SRC_SRC		=> Mage::helper('sagawa')->__('SRC'),
        );
    }

   /**
    * Public getter for Delivery Payment Source Types Array
    *
    * @return array
    */
    public function getDelPaySourceTypes()
    {
        return array(
            self::DEL_PAY_PRE	=> Mage::helper('sagawa')->__('Pre-Paid'),
            self::DEL_PAY_COD	=> Mage::helper('sagawa')->__('Cash on Delivery'),
        );
    }

   /**
    * Public getter for Pre-Payd Payment Source
    *
    * @return array
    */
    public function getPrePaySource()
    {
        return self::DEL_PAY_PRE;
    }

   /**
    * Public getter for COD Payment Source
    *
    * @return array
    */
    public function getCodPaySource()
    {
        return self::DEL_PAY_COD;
    }

   /**
    * Public getter for Store Slips Settings
    *
    * @return array
    */
    public function getStoreSlipSettings()
    {
		$data = array();
		// Get all Store Config Data necessary for the Slip data
		$data['customer_id']			= Mage::getStoreConfig('sagawa/slips/storeid');
		$data['store_name']				= Mage::getStoreConfig('sagawa/slips/name');
		$data['store_namekana']			= Mage::getStoreConfig('sagawa/slips/name_kana');
		$data['store_address_1']		= Mage::getStoreConfig('sagawa/slips/address_1');
		$data['store_address_2']		= Mage::getStoreConfig('sagawa/slips/address_2');
		$data['store_postcode']			= Mage::getStoreConfig('sagawa/slips/postcode');
		$data['store_contact']			= Mage::getStoreConfig('sagawa/slips/contact');
		$data['store_tel']				= Mage::getStoreConfig('sagawa/slips/tel');
		$data['ship_method']			= Mage::getStoreConfig('sagawa/slips/ship_method');
		$data['tax_amount']				= '';
		$data['ensured_amount']			= Mage::getStoreConfig('sagawa/slips/ensured_amount');
		$data['ensured_amount_printed']	= Mage::getStoreConfig('sagawa/slips/ensured_printed');
		$data['src_class']				= Mage::getStoreConfig('sagawa/slips/src_class');
		$data['branc_code']				= Mage::getStoreConfig('sagawa/slips/branc_code');
		// Return the Array
        return $data;
    }

   /**
    * Get the first 3 service code associated with the given slip data
    *
    * @param array $slipData Slip Data Collection
    *
    * @return array
    */
    public function getServicesCodes($slipData)
    {
		// Eligible Service Collection
		$allServices = array();
		$result = array();

		// Set Shipping method service code
		if (isset($slipData['ship_method'])){
			switch ($slipData['ship_method']) {
			    case self::SHP_METHOD_SUPEREXP:
					$allServices[] = self::SRV_CODE_SUPEREXP;
				break;
			    case self::SHP_METHOD_SOKUHAIEXP:
					$allServices[] = self::SRV_CODE_SOKUHAI;
				break;
			    case self::SHP_METHOD_AIRDAY:
					$allServices[] = self::SRV_CODE_AIR;
				break;
			    case self::SHP_METHOD_AIRMORNING:
					$allServices[] = self::SRV_CODE_AIR;
				break;
			    case self::SHP_METHOD_TIMEEXP:
					$allServices[] = self::SRV_CODE_TIMEEXP;
				break;
			}
		}

		// Set Cooling Shipping service code
		if (isset($slipData['cooling_shipment'])){
			switch ($slipData['cooling_shipment']) {
			    case self::COOL_SHIP_COOL:
					$allServices[] = self::SRV_CODE_COOL;
				break;
			    case self::COOL_SHIP_FROZE:
					$allServices[] = self::SRV_CODE_FROZE;
				break;
			}
		}

		// Set Delivery Type Service Code
		if (isset($slipData['cooling_shipment']) && $slipData['delivery_type'] == self::DEV_PICKUP) $allServices[] = self::SRV_CODE_PICKUP;

		// Set Delivery Day Service Code
		if (isset($slipData['delivery_date']) && !empty($slipData['delivery_date'])) $allServices[] = self::SRV_CODE_DELDAY;

		// Set Delivery Time service code
		if (isset($slipData['delivery_time'])){
			switch ($slipData['delivery_time']) {
			    case self::TIME_ZOME_5_1ST:
			    case self::TIME_ZOME_6_1ST:
					$allServices[] = self::SRV_CODE_DELTIMEMORNING;
				break;
				default:
					switch (Mage::getStoreConfig('sagawa/slips/ship_time_class')) {
					    case self::TIME_ZONE_5:
							$allServices[] = self::SRV_CODE_DELTIME5;
						break;
					    case self::TIME_ZONE_6:
							$allServices[] = self::SRV_CODE_DELTIME6;
						break;
					}
				break;
			}
		}

		// Set Cash on Delivery Payment Method service code
		if (isset($slipData['cod_method'])){
			switch ($slipData['cod_method']) {
			    case self::COD_PAY_ANY:
					$allServices[] = self::SRV_CODE_CODANY;
				break;
			    case self::COD_PAY_CASH:
					$allServices[] = self::SRV_CODE_CODCASH;
				break;
			    case self::COD_PAY_CARD:
					$allServices[] = self::SRV_CODE_CODCARD;
				break;
			}
		}

		// Set Fragile Status Service Code
		if (isset($slipData['fragile_status']) && !empty($slipData['fragile_status'])) $allServices[] = self::SRV_CODE_CARE;

		// Set Valuables Status Code
		if (isset($slipData['valuables_status']) && !empty($slipData['valuables_status'])) $allServices[] = self::SRV_CODE_VALUE;

		// Set Side Up Status Code
		if (isset($slipData['side_status']) && !empty($slipData['side_status'])) $allServices[] = self::SRV_CODE_SIDE;

		// Keep only the first 3 codes
		$allServices = array_slice($allServices, 0, 3);

		// Fill the returning array
		for ($i = 1; $i <= count($allServices); $i++) {
			$result['service_code_'.$i] = $allServices[$i-1];
		}

		// Returrn the result
		return $result;
	}

	/**
     * Set Delivery Date Options
     * 
     * @params string $post Current Post Data
     */
    public function setDeliveryOptions($post)
    {
		// Get Carrier helper Instance
		$helper = Mage::helper('sagawa');
				
		// Check if the Delivery Date Extension is Available
		$haveDD = Mage::helper('core')->isModuleEnabled(base64_decode($helper::DD_EXT));
		// If it is, set the information in the current session
		if($haveDD){
			// Define Returning Array
			$deliveryOptions = array();
			// Set data if available
			$deliveryOptions['delivery_date']		= isset($post['shipping_delivery_date'])		? $post['shipping_delivery_date']		: '';
			if ($deliveryOptions['delivery_date'] == '') return;
			
			// Get the posted value or ''
			$selectedTime = isset($post['shipping_delivery_time'])		? $post['shipping_delivery_time']		: '';
			// Get the closest value according to the posted value
			$deliveryOptions['delivery_time'] = $this->getNearestTime($selectedTime);

			$deliveryOptions['delivery_comment']	= isset($post['shipping_delivery_comments'])	? $post['shipping_delivery_comments']	: '';
			// Set returning array into current session if any available
			if (!empty($deliveryOptions)) Mage::getSingleton('sagawa/session')->setDeliveryOptions($deliveryOptions);
		}
	}

	/**
     * Set all customer selected Shippin options into the current Session
     * 
     * @params string $post Current Post Data
     */
    public function setCustomerOptions($post)
    {
		// Define Returning Array
		$customerOptions = array();
		// Set data if available
		$customerOptions['fragile_status']	= isset($post['fragile_value'])		? $post['fragile_value']	: self::BOOL_FALSE;
		$customerOptions['valuables_status']= isset($post['valuables_value'])	? $post['valuables_value']	: self::BOOL_FALSE;
		$customerOptions['side_status']		= isset($post['side_value'])		? $post['side_value']		: self::BOOL_FALSE;
		$customerOptions['cooling_shipment']= isset($post['cooler_value'])		? $post['cooler_value']		: self::COOL_SHIP_NONE;
		$customerOptions['cod_method']		= isset($post['cod_value'])			? $post['cod_value']		: self::BOOL_FALSE;
		$customerOptions['delivery_type']	= isset($post['delivery_value'])	? $post['delivery_value']	: self::BOOL_FALSE;
		// Set returning array into current session if any available
		if (!empty($customerOptions)) Mage::getSingleton('sagawa/session')->setCustomerOptions($customerOptions);
    }

   /**
    * Public getter for the nearest available time in the array
    *
    * @return string
    */
	public function getNearestTime($number)
	{
		// Get All Available Time
		$times = $this->getTimesZoneIndexesArray();

		// Chek if an exact match for the index
	    if (in_array($number, $times)) {
	         return $number;
	    }

	    $number = preg_replace("/[^0-9]/","",$number);
	    $number =  substr($number, 0, 2);
	    
	    if($number>24) $number =  substr($number, 0, 1);
	    
	    // Get All Available Time
	    $times = $this->getTimesZoneArray();
	
		// Check if the given value is smaler than the smalest available value in the array
		if ($number < 12 )  return '01';
	   
	    // Init the Closest value result as the smallest value
		$closest = '01';
		unset($times['01']);
		
		// Searc the nearest Number
	    foreach($times as $key=>$value)
	    {
	    	if($key == '01') continue;
	    	$val = preg_replace("/[^0-9]/","",$value);
		    $min = substr($val, 0, 2);
		    $max = substr($val, 4, 2);
		    if($number >= $min && $number <= $max){
		    	$closest = $key;
		    }
	    }
	
	    // Return the Result
		return $closest;
	}
}