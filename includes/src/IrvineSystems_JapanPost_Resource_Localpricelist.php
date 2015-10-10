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
 
class IrvineSystems_JapanPost_Resource_Localpricelist
{

    /**
     * Teikei Rates (定形郵便物)
     *
     * Ref: http://www.post.japanpost.jp/service/standard/one_price.html
     */
    protected $_teikeiRates = array(
		82,	// Fee for Packages up to 25 grams
		92	// Fee for Packages up to 50 grams
	);

    /**
     * TeikeiGai Rates (定形外郵便物)
     *
     * Ref: http://www.post.japanpost.jp/service/standard/one_price.html
     */
    protected $_teikeiGaiRates = array(
		120,	// Fee for Packages up to 50 grams
		140,	// Fee for Packages up to 100 grams
		205,	// Fee for Packages up to 150 grams
		250,	// Fee for Packages up to 150 grams
		400,	// Fee for Packages up to 500 grams
		600,	// Fee for Packages up to 1 Kg
		870,	// Fee for Packages up to 2 Kg
		1180	// Fee for Packages up to 4 Kg
	);

	/**
	 * Cooling Shipment price charges
	 */
	protected $_coolCharges = array(
		220,	// Fee for Packages with size up to 60cm
		350,	// Fee for Packages with size up to 80cm
		660,	// Fee for Packages with size up to 100cm
		660,	// Fee for Packages with size up to 120cm
		1300,	// Fee for Packages with size up to 140cm
		2060	// Fee for Packages with size up to 150cm
	);

    /**
     * YuuPack Rate Array
     *
     * Ref: http://www.post.japanpost.jp/fee/simulator/kokunai/parcel.html
     */
    protected $_yuuPackRates = array(
        // Size(cm)   60   80  100  120  140  160  170          
        'N0'=>array( 610, 810,1030,1230,1440,1650,1750), // Same Pref.	Nearest
        'N1'=>array( 710, 930,1130,1340,1540,1750,1950), // 1stZone		  ↑
        'N2'=>array( 810,1030,1230,1440,1650,1850,2060), // 2ndZone   
        'N3'=>array( 930,1130,1340,1540,1750,1950,2160), // 3rdZone
        'N4'=>array(1030,1230,1440,1650,1850,2060,2260), // 4thZone
        'N5'=>array(1130,1340,1540,1750,1950,2160,2370), // 5thZone
        'N6'=>array(1230,1440,1650,1850,2060,2260,2470), // 6thZone		  ↓
        'N7'=>array(1340,1540,1750,1950,2160,2370,2570), // 7thZone		farthest
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