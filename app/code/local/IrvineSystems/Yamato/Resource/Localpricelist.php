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
 
class IrvineSystems_Yamato_Resource_Localpricelist
{
	/**
	 * Cash on Delivery Fee Charges
	 */
	protected $_codCharges = array(
		300,	// Class 1 [Amount] - Cash on Delivery Fee for Order From JPY 1 To JPY 9,999.
		400,	// Class 2 [Amount] - Cash on Delivery Fee for Order From JPY 10,000 To JPY 30,000.
		600,	// Class 3 [Amount] - Cash on Delivery Fee for Order From JPY 30,000 To JPY 100,000.
		1000	// Class 4 [Amount] - Cash on Delivery Fee for Order From JPY 100,000 To JPY 300,000.
	);
		
	/**
	 * Cash on Delivery Tax Charges
	 */
	protected $_codTaxCharges = array(
		24,		// Class 1 [Tax] 	- Cash on Delivery Tax for Order From JPY 1 To JPY 9,999.
		32,		// Class 2 [Tax] 	- Cash on Delivery Tax for Order From JPY 10,000 To JPY 30,000.
		48,		// Class 3 [Tax] 	- Cash on Delivery Tax for Order From JPY 30,000 To JPY 100,000.
		80		// Class 4 [Tax] 	- Cash on Delivery Tax for Order From JPY 100,000 To JPY 300,000.
	);
		
	/**
	 * Mail Shipment price charges
	 * 
	 * Ref: http://www.kuronekoyamato.co.jp/mail/mail_level.html
	 */
	protected $_mailCharges = array(
		82,		// Bellow 10 cm
		164		// From 10 mm to 20cm
	);

	/**
	 * Cooling Shipment price charges
	 */
	protected $_coolCharges = array(
		216,	// Fee for Packages with size up to 60cm and weight up to 2Kg
		216,	// Fee for Packages with size up to 80cm and weight up to 5Kg
		324,	// Fee for Packages with size up to 100cm and weight up to 10Kg
		648		// Fee for Packages with size up to 120cm and weight up to 15Kg
	);

    /**
     * Basic Shipping Rates Relation in Shipping Dimension and pakage size
     *
     * Ref: http://www.post.yamato.jp/fee/simulator/kokunai/parcel.html
     */
    protected $_basicRates = array(
        // Size(cm)    60   80  100  120  140  160          
        'N0' =>array( 756, 972,1188,1404,1620,1836), // Same Pref.	Nearest
        'N1' =>array( 864,1080,1296,1512,1728,1944), //  1stZone		  ↑
        'N2' =>array( 972,1188,1404,1620,1836,2052), //  2ndZone
        'N3' =>array(1080,1296,1512,1728,1944,2160), //  3rdZone
        'N4' =>array(1188,1404,1620,1836,2052,2268), //  4thZone
        'N5' =>array(1296,1512,1728,1944,2160,2376), //  5thZone
        'N6' =>array(1404,1620,1836,2052,2268,2484), //  6thZone
        'N7' =>array(1512,1728,1944,2160,2376,2592), //  7thZone
        'N8' =>array(1620,1836,2052,2268,2484,2700), //  8thZone
        'N9' =>array(1728,1944,2160,2376,2592,2808), //  9thZone
        'N10'=>array(1836,2052,2268,2484,2700,2916), // 10thZone
        'N11'=>array(1188,1728,2268,2808,3348,3888), // 11thZone
        'N12'=>array(1296,1836,2376,2916,3780,3996), // 12thZone
        'N13'=>array(1404,1944,2484,3024,3564,4104), // 13thZone
        'N14'=>array(1512,2052,2592,3132,3672,4212), // 14thZone
        'N15'=>array(1620,2160,2700,3240,3780,4320), // 15thZone		  ↓
        'N16'=>array(1944,2484,3024,3564,4104,4644), // 16thZone		farthest
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