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

class IrvineSystems_Yamato_Model_Slips extends Mage_Rule_Model_Rule
{

    // Bool Types Constants
    const BOOL_FALSE			= 0;
    const BOOL_TRUE				= 1;

	// Delivery Mode Constants
    const DEV_MODE_TAQBIN		= '0';
	//const DEV_MODE_UKN		= '1';
	const DEV_MODE_TAQBIN_COD	= '2';
	const DEV_MODE_MAILBIN		= '3';
	const DEV_MODE_TAQBIN_TIME	= '4';
	const DEV_MODE_CHARGE		= '5';
	const DEV_MODE_MAILBIN_EXP	= '6';
	
	// Shipping Time Zone LONG Constants
	const TIME_ZOME_LONG_0		= '0000';
	const TIME_ZOME_LONG_1ST	= '0812';
	const TIME_ZOME_LONG_2ND	= '1214';
	const TIME_ZOME_LONG_3RD	= '1416';
	const TIME_ZOME_LONG_4TH	= '1618';
	const TIME_ZOME_LONG_5TH	= '1820';
	const TIME_ZOME_LONG_6TH	= '2021';
	
    // Cooling Shipment Types Constants
    const COOL_SHIP_NONE		= 0;
    const COOL_SHIP_COOL		= 1;
    const COOL_SHIP_FROZE		= 2;

    // Email Devices Types Constants
    const MAIL_PC			= 1;
    const MAIL_MOBILE		= 2;

    /**
     * Model Constructor
     * 
     */
    public function _construct()
    {
        // Construct parent
        parent::_construct();
        // Initialize cartrules collection
        $this->_init('yamato/slips');
    }

   /**
    * Public getter for Bool Types Array
    *
    * @return array
    */
    public function getBoolTypes()
    {
        return array(
            self::BOOL_FALSE		=> Mage::helper('yamato')->__('FALSE'),
            self::BOOL_TRUE			=> Mage::helper('yamato')->__('TRUE'),
        );
    }

   /**
    * Public getter for Mail Device Types Array
    *
    * @return array
    */
    public function getMailDeviceTypes()
    {
        return array(
            self::MAIL_PC			=> Mage::helper('yamato')->__('PC Email Address'),
            self::MAIL_MOBILE		=> Mage::helper('yamato')->__('Mobile Email Address'),
        );
    }

    /**
     * Public getter for Delivary Mode Types Array
     *
     * @return array
     */
    public function getDelivaryModeTypes()
    {
    	return array(
   			self::DEV_MODE_TAQBIN		=> Mage::helper('yamato')->__('Ta-Q-BIN'),
   			//self::DEV_MODE_UKN		=> Mage::helper('yamato')->__('Unknown'),
   			self::DEV_MODE_TAQBIN_COD	=> Mage::helper('yamato')->__('Ta-Q-BIN Cash on Delivery'),
    		self::DEV_MODE_MAILBIN		=> Mage::helper('yamato')->__('Mail-Bin'),
   			self::DEV_MODE_TAQBIN_TIME	=> Mage::helper('yamato')->__('Ta-Q-BIN Time Service'),
   			self::DEV_MODE_CHARGE		=> Mage::helper('yamato')->__('CAHRGE'),
   			self::DEV_MODE_MAILBIN_EXP	=> Mage::helper('yamato')->__('Mail-Bin Express')
    	);
    }

    /**
     * Public getter for Currently Selected Time Zone Indexes
     *
     * @return array
     */
    public function getTimesZoneIndexesArray()
    {
    	return $this->getTimeZoneLongIndexes();
    }
    
    /**
     * Public getter for Currently Selected Time Zone
     *
     * @return array
     */
    public function getTimesZoneArray()
    {
    	return $this->getTimeZoneLongTypes();
    }
    
   /**
    * Public getter for Currently Selected Times
    *
    * @return array
    */
    public function getTimesArray()
    {
    	$allowedValues = $this->getTimesZoneArray();
    	$len = count($allowedValues);
    	$returnValues = array_slice($allowedValues, 1, $len, true);
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
    	$times = $this->getTimesZoneIndexesArray();
    	
    	// Chek if an there is a match for the index
    	if (in_array($index, $times)) {
    		// Return the string for the Time zone array
    		$allowedValues = $this->getTimesZoneArray();
   			return $allowedValues[$index];
    	}

    	// Return null if nothing is found
    	return null;
    }
    
    /**
    * Public getter for Time Zone Long Types Array
    *
    * @return array
    */
    public function getTimeZoneLongTypes($includenull = false)
    {
    	return array(
        	self::TIME_ZOME_LONG_0		=> '',
        	self::TIME_ZOME_LONG_1ST	=> Mage::helper('yamato')->__('Morning'),
			self::TIME_ZOME_LONG_2ND	=> Mage::helper('yamato')->__('12:00～14:00'),
			self::TIME_ZOME_LONG_3RD	=> Mage::helper('yamato')->__('14:00～16:00'),
			self::TIME_ZOME_LONG_4TH	=> Mage::helper('yamato')->__('16:00～18:00'),
			self::TIME_ZOME_LONG_5TH	=> Mage::helper('yamato')->__('18:00～20:00'),
			self::TIME_ZOME_LONG_6TH	=> Mage::helper('yamato')->__('19:00～21:00'),
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
        	self::TIME_ZOME_LONG_1ST,
			self::TIME_ZOME_LONG_2ND,
			self::TIME_ZOME_LONG_3RD,
			self::TIME_ZOME_LONG_4TH,
			self::TIME_ZOME_LONG_5TH,
			self::TIME_ZOME_LONG_6TH,
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
            self::COOL_SHIP_NONE	=> Mage::helper('yamato')->__('Default'),
            self::COOL_SHIP_COOL	=> Mage::helper('yamato')->__('Cool'),
            self::COOL_SHIP_FROZE	=> Mage::helper('yamato')->__('Frozen'),
        );
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
		$data['store_member_num']		= Mage::getStoreConfig('yamato/slips/member_num');
		$data['store_tel']				= Mage::getStoreConfig('yamato/slips/tel');
		$data['store_tel_branch_num']	= Mage::getStoreConfig('yamato/slips/tel_branch_num');
		$data['store_postcode']			= Mage::getStoreConfig('yamato/slips/postcode');
		$data['store_address']			= Mage::getStoreConfig('yamato/slips/address');
		$data['store_apart_name']		= Mage::getStoreConfig('yamato/slips/apart_name');
		$data['store_name']				= Mage::getStoreConfig('yamato/slips/name');
		$store_full_name_kana			= Mage::getStoreConfig('yamato/slips/name_kana');
		/* r 	「全角」英字を「半角」に変換します。
		 * n 	「全角」数字を「半角」に変換します。
		 * a 	「全角」英数字を「半角」に変換します。
		 * s 	「全角」スペースを「半角」に変換します（U+3000 -> U+0020）。
		 * k 	「全角カタカナ」を「半角カタカナ」に変換します。
		 * h 	「全角ひらがな」を「半角カタカナ」に変換します。
		 */
		$store_full_name_kana = mb_convert_kana( $store_full_name_kana, 'ahnrsk',"utf-8");
		$data['store_name_kana']	= $store_full_name_kana;
		$data['invoice_customer_id']	= Mage::getStoreConfig('yamato/slips/invoice_customer_id');
		$data['invoice_class_id']		= Mage::getStoreConfig('yamato/slips/invoice_class_id');
		$data['shipping_charge_number']	= Mage::getStoreConfig('yamato/slips/shipping_charge_number');
		// Return the Array
        return $data;
    }

    /**
    * Get the mail message to schedule from config
    * Convertred in zen-kaku format
   	* R 	「半角」英字を「全角」に変換します。
	* N 	「半角」数字を「全角」に変換します。
	* A 	「半角」英数字を「全角」に変換します （"a", "A" オプションに含まれる文字は、U+0022, U+0027, U+005C, U+007Eを除く U+0021 - U+007E の範囲です）。
	* S 	「半角」スペースを「全角」に変換します（U+0020 -> U+3000）。
	* K 	「半角カタカナ」を「全角カタカナ」に変換します。
	* H 	「半角カタカナ」を「全角ひらがな」に変換します。
	* V 	濁点付きの文字を一文字に変換します。"K", "H" と共に使用します。
    *
    * @return string
    */
    protected function getMessageToSchedule(){
    	$message = Mage::getStoreConfig('yamato/slips/mail_message_to_schedule');
    	$message = mb_convert_kana( $message, 'RNASKHV',"utf-8");
    	return $message;
    }

    /**
    * Get the mail message to schedule from config
    * Convertred in zen-kaku format
   	* R 	「半角」英字を「全角」に変換します。
	* N 	「半角」数字を「全角」に変換します。
	* A 	「半角」英数字を「全角」に変換します （"a", "A" オプションに含まれる文字は、U+0022, U+0027, U+005C, U+007Eを除く U+0021 - U+007E の範囲です）。
	* S 	「半角」スペースを「全角」に変換します（U+0020 -> U+3000）。
	* K 	「半角カタカナ」を「全角カタカナ」に変換します。
	* H 	「半角カタカナ」を「全角ひらがな」に変換します。
	* V 	濁点付きの文字を一文字に変換します。"K", "H" と共に使用します。
    *
    * @return string
    */
    protected function getMessageToComplete(){
    	$message = Mage::getStoreConfig('yamato/slips/mail_message_to_complete');
    	$message = mb_convert_kana( $message, 'RNASKHV',"utf-8");
    	return $message;
    }

	/**
     * Set Delivery Date Options
     * 
     * @params string $post Current Post Data
     */
    public function setDeliveryOptions($post)
    {
		// Get Carrier helper Instance
		$helper = Mage::helper('yamato');
		// Check if the Delivery Date Extension is Available
		$haveDD = Mage::helper('core')->isModuleEnabled(base64_decode($helper::DD_EXT));
		
		// If it is, set the information in the current session
		if($haveDD){
			// Define Returning Array
			$deliveryOptions = array();
			// Set data if available

			$deliveryOptions['delivery_date']		= isset($post['shipping_delivery_date'])		? $post['shipping_delivery_date']		: '';
			// no delivery date was selected stop processing
			if ($deliveryOptions['delivery_date'] == '') return;
			
			// Get the posted value or ''
			$selectedTime = isset($post['shipping_delivery_time'])		? $post['shipping_delivery_time']		: '';
			// Get the closest value according to the posted value
			$deliveryOptions['delivery_time'] = $this->getNearestTime($selectedTime);

			// Set the comment value
			$deliveryOptions['delivery_comment']	= isset($post['shipping_delivery_comments'])	? $post['shipping_delivery_comments']	: '';
			// Set returning array into current session if any available
			if (!empty($deliveryOptions)) Mage::getSingleton('yamato/session')->setDeliveryOptions($deliveryOptions);
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
    	$customerOptions['cool_type']							= isset($post['cooler_value'])		? $post['cooler_value']		: self::COOL_SHIP_NONE;
    	$customerOptions['handling_1']							= isset($post['handling1_value'])	? $post['handling1_value']	: '';
    	$customerOptions['handling_2']							= isset($post['handling2_value'])	? $post['handling2_value']	: '';
    	$customerOptions['held_yamato_office']					= isset($post['held_yamato_office_value'])	? $post['held_yamato_office_value']	: '';
    	$customerOptions['yamato_office_id'] 					= isset($post['yamato_office_id_value'])	? $post['yamato_office_id_value']	: '';
    	$customerOptions['enable_email_notice_schedule']		= isset($post['enable_email_to_notice_schedule_value'])	? $post['enable_email_to_notice_schedule_value']	: '0';
    	if($customerOptions['enable_email_notice_schedule']==1){
    		$customerOptions['input_equipment']					= isset($post['input_equipment_value'])	? $post['input_equipment_value']	: '';
    		$customerOptions['email_notice_schedule']			= isset($post['email_to_notice_schedule_value'])	? $post['email_to_notice_schedule_value']	: '';
			$customerOptions['email_message_notice_schedule']	= $this->getMessageToSchedule()	? $this->getMessageToSchedule() : '';
    	}
    	$customerOptions['enable_email_notice_complete']		= isset($post['enable_email_to_notice_complete_value'])	? $post['enable_email_to_notice_complete_value']	: '0';
    	if($customerOptions['enable_email_notice_complete']==1){
    		$customerOptions['email_notice_complete']			= isset($post['email_to_notice_complete_value'])	? $post['email_to_notice_complete_value']	: $customer_email_address;
			$customerOptions['email_message_notice_complete']	= $this->getMessageToComplete()	? $this->getMessageToComplete() : '';
    	}
    	// Set returning array into current session if any available
    	if (!empty($customerOptions)) Mage::getSingleton('yamato/session')->setCustomerOptions($customerOptions);
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

	    // Preg the number string to only numbers
	    $number = preg_replace("/[^0-9]/","",$number);
	      
		// Sort the array
	    sort($times);
	
	   // Check if the given value is smaler than the smalest available value in the array
	   if ($number < $times[0] ) 
	   { 
		   return $times[0];
	   }
	
	    // Init the Closest value result as the smallest value
		$closest = $times[0];
	
		// Searc the nearest Number
	    foreach($times as $value)
	    {
	        if(abs($number - $closest) > abs($value - $number))
	        {
	            $closest = $value;
	        }
	    }
	
	    // Return the Result
		return $closest;
	}
}