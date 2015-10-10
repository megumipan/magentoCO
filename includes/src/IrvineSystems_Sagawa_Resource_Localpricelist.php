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
 
class IrvineSystems_Sagawa_Resource_Localpricelist
{
	/**
	 * Cash on Delivery Charges
	 */
	protected $_codCharges = array(
		324,	// Class 1 - Cash on Delivery Fee for Order From JPY 1 To JPY 10,000.
		432,	// Class 3 - Cash on Delivery Fee for Order From JPY 10,001 To JPY 30,000.
		648,	// Class 10 - Cash on Delivery Fee for Order From JPY 30,001 To JPY 100,000.
		1080,	// Class 30 - Cash on Delivery Fee for Order From JPY 100,001 To JPY 300,000.
		2160,	// Class 50 - Cash on Delivery Fee for Order From JPY 300,001 To JPY 500,000.
		3240,	// Class 100 - Cash on Delivery Fee for Order From JPY 500,001 To JPY 1,000,000.
		4536	// Class MAX - Cash on Delivery Fee for Order Over JPY 1,000,000.
	);

	/**
	 * Mail Shipment price charges
	 */
	protected $_mailCharges = array(
		165,	// Fee for Packages up to 300 grams
		216,	// Fee for Packages up to 600 grams
		319		// Fee for Packages up to 1000 grams
	);

	/**
	 * Cooling Shipment price charges
	 */
	protected $_coolCharges = array(
		162,	// Fee for Packages with size up to 60cm and weight up to 2Kg
		162,	// Fee for Packages with size up to 80cm and weight up to 5Kg
		270,	// Fee for Packages with size up to 100cm and weight up to 10Kg
		324,	// Fee for Packages with size up to 140cm and weight up to 20Kg
		540,	// Fee for Packages with size up to 140cm and weight up to 30Kg
		756,	// Fee for Packages with size up to 140cm and weight up to 40Kg
		972		// Fee for Packages with size up to 140cm and weight up to 50Kg
	);

    /**
     * Basic Shipping Rates Relation in Shipping Dimension and pakage size
     *
     * Ref: http://www.post.sagawa.jp/fee/simulator/kokunai/parcel.html
     */
    protected $_basicRates = array(
        // Size(cm)    60   80  100  140  160  170  180  200  220  240  260
        'N0' =>array( 756,1026,1296,1566,1836,2376,2646,3186,3726,4806,5886), // Same Pref.	Nearest
        'N1' =>array( 864,1134,1404,1674,1944,2484,2754,3294,3834,44914,5994), // 1stZone		  ↑
        'N2' =>array( 972,1242,1512,1782,2052,2592,2862,3402,3942,5022,6102), // 2ndZone   
        'N3' =>array(1080,1350,1620,1890,2160,2700,2970,3510,4050,5130,6210), // 3rdZone
        'N4' =>array(1188,1458,1728,1998,2268,2808,3078,3618,4158,5238,6318), // 4thZone
        'N5' =>array(1296,1566,1836,2106,2376,2916,3186,3726,4266,5346,6426), // 5thZone
    	'N6' =>array(1404,1674,1944,2214,2484,3024,2394,3834,4374,5454,6534), // 6thZone
    	'N7' =>array(1512,1782,2052,2322,2592,3132,3102,3942,4482,5562,6642), // 7thZone
    	'N8' =>array(1620,1890,2160,2430,2700,3240,3510,4050,4590,5670,6750), // 8thZone
        'N9' =>array(1728,1998,2268,2538,2808,3348,3618,4158,4698,5778,6858), // 9thZone		  ↓
        'N10'=>array(1836,2106,2376,2646,2916,3456,3726,4266,4806,5886,6966), // 10thZone		farthest
    );

	/**
	 * add price by weight for Free Size Shipping
	 */
	protected $_freeSizeShippingAddPrice = array(
		30000,	// Starting weight in which the Free Size fee will be applied
		10000,	// Multiplier weight (an additional fee will be added every time the cart weight overpass the stndar weight by this value)
		270		// Free sixe fee
	);
	
    
    /**
     * Public Getter For Rate Proprierties
     * ** DO NOT MODIFY, OTHERWISE QUOTE WILL NOT BE ABLE TO BE GENERATED **
     *
	 * @var $property proprierty to be return
     */
    public function __get($property) {
    	if (property_exists($this, $property)) return $this->$property;
  	}
}