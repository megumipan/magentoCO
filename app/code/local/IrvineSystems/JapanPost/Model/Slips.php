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

class IrvineSystems_JapanPost_Model_Slips extends Mage_Rule_Model_Rule
{
    // Bool Types Constants
    const BOOL_FALSE		= 0;
    const BOOL_TRUE			= 1;

    // Delivery Mode Types Constants
    const DEL_MODE_NO		= 0;
    const DEL_MODE_EXP		= 1;
    const DEL_MODE_SPEC		= 2;
    const DEL_MODE_DES		= 3;

    // Delivery Services Types Constants
    const DEL_SERV_NO		= 0;
    const DEL_SERV_TRACK	= 1;
    const DEL_SERV_SEC		= 2;

    // Delivery Payments Source Types Constants
    const DEL_PAY_PRE		= 0;
    const DEL_PAY_COD		= 1;

    // Delivery Types Constants
    const DEL_PICKUP		= 0;
    const DEL_DELIVER		= 1;
    const DEL_UNSPEC		= 2;

    // Mail Class Types Constants
    const MAIL_CLASS_YUPACK	= 0;
    const MAIL_CLASS_YUMAIL	= 1;
    const MAIL_CLASS_STD	= 2;
    const MAIL_CLASS_CUSTOM	= 3;
    const MAIL_CLASS_PARCEL	= 4;
    const MAIL_CLASS_LABEL	= 9;

    // Cooling Shipment Types Constants
    const COOL_SHIP_NONE	= 0;
    const COOL_SHIP_COOL	= 1;
    const COOL_SHIP_FROZE	= 2;

    // Discount Types Constants
    const DISCOUNT_UNSPEC	= 0;
    const DISCOUNT_SAME		= 1;
    const DISCOUNT_MULTI	= 2;

    // Taxable Types Constants
    const TAX_APPLY			= 0;
    const TAX_NOAPPLY		= 1;

    // Store Prefix Types Constants
    const PREFIX_SIR		= 1;
    const PREFIX_MR			= 2;
    const PREFIX_MESSRS		= 3;
    const PREFIX_TO1		= 4;
    const PREFIX_TO2		= 5;
    const PREFIX_TO3		= 6;
    const PREFIX_NONE		= 9;

    /**
     * Model Constructor
     * 
     */
    public function _construct()
    {
        // Construct parent
        parent::_construct();
        // Initialize cartrules collection
        $this->_init('japanpost/slips');
    }

   /**
    * Public getter for Bool Types Array
    *
    * @return array
    */
    public function getBoolTypes()
    {
        return array(
            self::BOOL_FALSE		=> Mage::helper('japanpost')->__('FALSE'),
            self::BOOL_TRUE			=> Mage::helper('japanpost')->__('TRUE'),
        );
    }

   /**
    * Public getter for Delivery Modes Types Array
    *
    * @return array
    */
    public function getDelModTypes()
    {
        return array(
            self::DEL_MODE_NO		=> Mage::helper('japanpost')->__('None'),
            self::DEL_MODE_EXP		=> Mage::helper('japanpost')->__('Express'),
            self::DEL_MODE_SPEC		=> Mage::helper('japanpost')->__('Specified delivery date'),
            self::DEL_MODE_DES		=> Mage::helper('japanpost')->__('Desired delivery date'),
        );
    }

   /**
    * Public getter for Desired Delivery Index
    *
    * @return int
    */
    public function getDesiredDel()
    {
        return self::DEL_MODE_DES;
    }

   /**
    * Public getter for Delivery Services Types Array
    *
    * @return array
    */
    public function getDelSerTypes()
    {
        return array(
            self::DEL_SERV_NO		=> Mage::helper('japanpost')->__('None'),
            self::DEL_SERV_TRACK	=> Mage::helper('japanpost')->__('Track Delivery'),
            self::DEL_SERV_SEC		=> Mage::helper('japanpost')->__('Registered/Security'),
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
            self::DEL_PAY_PRE		=> Mage::helper('japanpost')->__('Pre-Paid'),
            self::DEL_PAY_COD		=> Mage::helper('japanpost')->__('Cash on Delivery'),
        );
    }

   /**
    * Public getter for Pre-Paid Delivery Pyament Source Types
    *
    * @return int
    */
    public function getDelPrePaidSourceType()
    {
        return self::DEL_PAY_PRE;
    }
    
   /**
    * Public getter for Cash on Delivery Pyament Source Types
    *
    * @return int
    */
    public function getDelCodSourceType()
    {
        return self::DEL_PAY_COD;
    }
    
    /**
    * Public getter for Delivery Types Array
    *
    * @return array
    */
    public function getDelTypes()
    {
        return array(
            self::DEL_PICKUP		=> Mage::helper('japanpost')->__('Pickup'),
            self::DEL_DELIVER		=> Mage::helper('japanpost')->__('Deliver'),
            self::DEL_UNSPEC		=> Mage::helper('japanpost')->__('Unspecified'),
        );
    }

   /**
    * Public getter for Mail Classes Types Array
    *
    * @return array
    */
    public function getMailClassTypes()
    {
        return array(
            self::MAIL_CLASS_YUPACK	=> Mage::helper('japanpost')->__('Yuu-Pack'),
            self::MAIL_CLASS_YUMAIL	=> Mage::helper('japanpost')->__('Yuu-Mail'),
            self::MAIL_CLASS_STD	=> Mage::helper('japanpost')->__('Standard Box'),
            self::MAIL_CLASS_CUSTOM	=> Mage::helper('japanpost')->__('Custom Box'),
            self::MAIL_CLASS_PARCEL	=> Mage::helper('japanpost')->__('Post Parcel'),
            self::MAIL_CLASS_LABEL	=> Mage::helper('japanpost')->__('Label'),
        );
    }

   /**
    * Public getter for YuuPack Mail Class Index
    *
    * @return int
    */
    public function getMailYuupackClass()
    {
        return self::MAIL_CLASS_YUPACK;
    }

   /**
    * Public getter for Label Mail Class Index
    *
    * @return int
    */
    public function getMailLabelClass()
    {
        return self::MAIL_CLASS_LABEL;
    }

   /**
    * Public getter for Cooling Shipment Types Array
    *
    * @return array
    */
    public function getCoolShipTypes()
    {
        return array(
            self::COOL_SHIP_NONE	=> Mage::helper('japanpost')->__('None'),
            self::COOL_SHIP_COOL	=> Mage::helper('japanpost')->__('Cool'),
            self::COOL_SHIP_FROZE	=> Mage::helper('japanpost')->__('Frozen'),
        );
    }

   /**
    * Public getter for Discount Types Array
    *
    * @return array
    */
    public function getDiscountTypes()
    {
        return array(
            self::DISCOUNT_UNSPEC	=> Mage::helper('japanpost')->__('Unspecified '),
            self::DISCOUNT_SAME		=> Mage::helper('japanpost')->__('Same Destination Discount'),
            self::DISCOUNT_MULTI	=> Mage::helper('japanpost')->__('MultiShipping'),
        );
    }

   /**
    * Public getter for Taxable Types Array
    *
    * @return array
    */
    public function getTaxTypes()
    {
        return array(
            self::TAX_APPLY			=> Mage::helper('japanpost')->__('Apply'),
            self::TAX_NOAPPLY		=> Mage::helper('japanpost')->__('Not Apply'),
        );
    }

   /**
    * Public getter for Prefix Types Array
    *
    * @return array
    */
    public function getPrefixTypes()
    {
        return array(
            self::PREFIX_SIR			=> Mage::helper('japanpost')->__('Sir/Lady'),
            self::PREFIX_MR				=> Mage::helper('japanpost')->__('Mr./Miss'),
            self::PREFIX_MESSRS			=> Mage::helper('japanpost')->__('Messrs/Mme'),
            self::PREFIX_TO1			=> Mage::helper('japanpost')->__('To'),
            self::PREFIX_TO2			=> Mage::helper('japanpost')->__('To '),
            self::PREFIX_TO3			=> Mage::helper('japanpost')->__('To  '),
            self::PREFIX_NONE			=> Mage::helper('japanpost')->__('None'),
        );
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
    	// Japan post do not have specific time therefore it will always return null
    	// The method is present for compatibility
    	// Return null if nothing is found
    	return null;
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
		$data['store_postcode']		= Mage::getStoreConfig('japanpost/slips/postcode');
		$data['store_address']		= Mage::getStoreConfig('japanpost/slips/address');
		$data['store_name']			= Mage::getStoreConfig('japanpost/slips/name');
		$data['store_namekana']		= Mage::getStoreConfig('japanpost/slips/name_kana');
		$data['store_prefix']		= Mage::getStoreConfig('japanpost/slips/prefix');
		$data['store_tel']			= Mage::getStoreConfig('japanpost/slips/tel');
		$data['store_email']		= Mage::getStoreConfig('japanpost/slips/email');
		$data['store_memberid']		= Mage::getStoreConfig('japanpost/slips/member_number');
		$data['discount_type']		= Mage::getStoreConfig('japanpost/slips/discount_type');
		$data['ship_service']		= Mage::getStoreConfig('japanpost/slips/ship_service');
		$data['ensured_amount']		= Mage::getStoreConfig('japanpost/slips/ensured_amount');
		$data['delivery_mode']		= Mage::getStoreConfig('japanpost/slips/delivery_mode');
		$data['taxable']			= Mage::getStoreConfig('japanpost/slips/taxable');
		// Return the Array
        return $data;
    }

	/**
     * Set Delivery Date Options
     * 
     * @params string $post Current Post Data
     */
    public function setDeliveryOptions($post)
    {
		// Get Carrier helper Instance
		$helper = Mage::helper('japanpost');
				
		// Check if the Delivery Date Extension is Available
		$haveDD = Mage::helper('core')->isModuleEnabled(base64_decode($helper::DD_EXT));
		// If it is, set the information in the current session
		if($haveDD){
			// Define Returning Array
			$deliveryOptions = array();
			// Set data if available
			$deliveryOptions['delivery_date']		= isset($post['shipping_delivery_date'])		? $post['shipping_delivery_date']		: '';
			$deliveryOptions['delivery_time']		= isset($post['shipping_delivery_time'])		? $post['shipping_delivery_time']		: '';
			$deliveryOptions['delivery_comment']	= isset($post['shipping_delivery_comments'])	? $post['shipping_delivery_comments']	: '';
			// Set returning array into current session if any available
			if (!empty($deliveryOptions)) Mage::getSingleton('japanpost/session')->setDeliveryOptions($deliveryOptions);
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
		$customerOptions['fragile_status']		= isset($post['fragile_value'])	? $post['fragile_value']	: '';
		$customerOptions['creature_status']		= isset($post['creature_value'])? $post['creature_value']	: '';
		$customerOptions['glass_status']		= isset($post['glass_value'])	? $post['glass_value']		: '';
		$customerOptions['side_status']			= isset($post['side_value'])	? $post['side_value']		: '';
		$customerOptions['weight_status']		= isset($post['weight_value'])	? $post['weight_value']		: '';
		$customerOptions['notification_post']	= isset($post['post_value'])	? $post['post_value']		: '';
		$customerOptions['notification_email']	= isset($post['email_value'])	? $post['email_value']		: '';
		$customerOptions['delivery_type']		= isset($post['delivery_value'])? $post['delivery_value']	: '';
		$customerOptions['ship_cooler']			= isset($post['cool_shipments_value'])? $post['cool_shipments_value']	: '';

		// Set returning array into current session if any available
		if (!empty($customerOptions)) Mage::getSingleton('japanpost/session')->setCustomerOptions($customerOptions);
	}

}