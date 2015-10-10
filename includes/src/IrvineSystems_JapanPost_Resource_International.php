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
 
class IrvineSystems_JapanPost_Resource_International
{
	/**
	* Get The Shipping Method Basic Rate
	*
	* @param $cartWeight = Cart Weight
	* @param $method_code = Unique code for the Shipping Method
	* @param $Zone = Shipping Zone Index
	* @return int
	*/
	public function getRate($cartWeight,$method_code,$Zone)
	{
		// Get the Standard Weight Classes for the current Method
		$weightClasses = $this->getStdWeightClasses($method_code);
		// Get the specifit Weight Class according to the CartWeight
		$weightClass = $this->getWeightClass($cartWeight,$weightClasses);

		// Get the method Standard Prices List
		switch($method_code){
		    case ('ems'):
				$prices = $this->_emsPrices();
		    break;
		    case ('airmail'):
				$prices = $this->_airPrices('STD');
		    break;
		    case ('sal'):
				$prices = $this->_salPrices('STD');
		    break;
		    case ('surface'):
				$prices = $this->_surPrices('STD');
		    break;
		}
		// Return the Rate for the requested WeightClass and Zone
		return $prices[$weightClass][$Zone];
	}


	/**
	* Get The Shipping Method Bonus Rate
	*
	* @param $cartWeight = Cart Weight
	* @param $method_code = Unique code for the Shipping Method
	* @param $Zone = Shipping Zone Index
	* @return int
	*/
	public function getBonusRate($cartWeight,$method_code,$Zone)
	{
		
		// Get the Bonus Weight Classes for the current Method
		$weightClasses = $this->_bonusWeightClasses($method_code);
		// Get the specifit Weight Class according to the CartWeight
		$weightClass = $this->getWeightClass($cartWeight,$weightClasses);
		
		// Get the method Bonus Prices List
		switch($method_code){
		    case ('airmail'):
				$prices = $this->_airPrices('BONUS');
		    break;
		    case ('sal'):
				$prices = $this->_salPrices('BONUS');
		    break;
		    case ('surface'):
				$prices = $this->_surPrices('BONUS');
		    break;
		}
		
		// Return the Rate for the requested WeightClass and Zone
		return $prices[$weightClass][$Zone];
	}


	/**
	* Get The ZoneIds for the Methods for the given Order weight
	*
	* @param $cartWeight = Cart Weight
	* @param $maxWeight = Weight Limit
	* @return array
	*/
	public function _getZoneIds($cartWeight,$maxWeight)
	{
		// Set the ZoneIds according to the Cart Weight
		switch(true){
		    case ($cartWeight <= 10000):
				return $this->_zoneIds('low');
		    break;
		    case ($cartWeight <= 20000):
				return $this->_zoneIds('mid');
		    break;
		    case ($cartWeight <= $maxWeight):
				return $this->_zoneIds('high');
		    break;
		}
		// if incorrect values were passed return null
		return null;
	}


	/**
	* Get The Weight Class for given Cart Weight
	*
	* @param $cartWeight = Cart Weight
	* @param $weightClasses = Weight Classes Array
	* @return int
	*/
	public function getWeightClass($cartWeight,$weightClasses)
	{
		// Sort the Weight Classes Array
		sort($weightClasses);

		// Get the first highest value in the array compare to the CartWeight
	    foreach ($weightClasses as $weightClass) {
	        if ($weightClass >= $cartWeight) return $weightClass;
	    }
		// If nothing is found return null
	    return NULL;
	}


	/**
	* Get The Standard Weight Classes for the Shipping Method
	*
	* @param $method_code = Unique code for the Shipping Method
	* @return array
	*/
	public function getStdWeightClasses($method_code)
	{
		// Check if the current method is EMS
		// EMS have different weight classes compare to other methods
	    if($method_code == 'ems')
			return $this->_stdWeightClasses('ems');

		// if is not EMS return the other classes
	    return $this->_stdWeightClasses('other');
	}

    /***************************************************************************************************
     ************************************ SHIPPING METHODS DATA ****************************************
     ***************************************************************************************************/

	/**
	* Zone Ids for the 3 Dimension Levels
	*
	* @param $lvl = Level Required
	* @return array
	*/
	private function _zoneIds($lvl) {
		// ZoneIds Array
		$zoneIds = array(
			'low' => array(
				'ems'		=> 1,
				'airmail'	=> 2,
				'sal'		=> 3,
				'surface'	=> 4
				),
			'mid' => array(
				'ems'		=> 5,
				'airmail'	=> 6,
				'sal'		=> 7,
				'surface'	=> 8
				),
			'high' => array(
				'ems'		=> 9,
				'airmail'	=> 10,
				'sal'		=> 11,
				'surface'	=> 12
				)
		);
		// Return null if the requested Level do not exist
		if(!array_key_exists($lvl, $zoneIds)) {
			return NULL;
		}
		// Return the requested Level
		return $zoneIds[$lvl];
	}


	/**
	* Stabdard Weight Classes
	*
	* @param $method = shipping method required
	* @return array
	*/
	private function _stdWeightClasses($type) {
		// Bonus Weight Classes Array
		$weightClasses = array(
			'ems'	=> array(300, 500, 600, 700, 800, 900, 1000, 1250, 1500, 1750, 2000, 2500, 3000, 3500, 4000, 4500, 5000, 5500, 6000, 7000, 8000, 9000, 10000, 11000, 12000, 13000, 14000, 15000, 16000, 17000, 18000, 19000, 20000, 21000, 22000, 23000, 24000, 25000, 26000, 27000, 28000, 29000, 30000),
			'other'		=> array(500, 1000, 1500, 2000, 2500, 3000, 3500, 4000, 4500, 5000, 5500, 6000, 6500, 7000, 7500, 8000, 8500, 9000, 9500, 10000, 11000, 12000, 13000, 14000, 15000, 16000, 17000, 18000, 19000, 20000, 21000, 22000, 23000, 24000, 25000, 26000, 27000, 28000, 29000, 30000)
		);
		// Return null if the requested Level do not exist
		if(!array_key_exists($type, $weightClasses)) {
			return NULL;
		}
		// Return the requested Level
		return $weightClasses[$type];
	}


	/**
	* Bonus Weight Classes
	*
	* @param $method = shipping method required
	* @return array
	*/
	private function _bonusWeightClasses($method) {
		// Bonus Weight Classes Array
		$weightClasses = array(
			'airmail'	=> array(50, 100, 150, 200, 250, 300, 350, 400, 450, 500, 550, 600, 650, 700, 750, 800, 850, 900, 950, 1000, 1250, 1500, 1750, 2000),
			'sal'		=> array(100, 200, 300, 400, 500, 600, 700, 800, 900, 1000, 1100, 1200, 1300, 1400, 1500, 1600, 1700, 1800, 1900, 2000),
			'surface'	=> array(100, 250, 500, 1000, 2000)
		);
		// Return null if the requested Level do not exist
		if(!array_key_exists($method, $weightClasses)) {
			return NULL;
		}
		// Return the requested Level
		return $weightClasses[$method];
	}


	/**
	* EMS Prices List
	*
	* @return array
	*/
	private function _emsPrices() {
	
		// Fill the Price List Array
		$prices = array(
			'300'=>array (900, 1200, 1500, 1700),
			'500'=>array (1100, 1500, 1800, 2100),
			'600'=>array (1240, 1680, 2000, 2440),
			'700'=>array (1380, 1860, 2200, 2780),
			'800'=>array (1520, 2040, 2400, 3120),
			'900'=>array (1660, 2220, 2600, 3460),
			'1000'=>array (1800, 2400, 2800, 3800),
			'1250'=>array (2100, 2800, 3250, 4600),
			'1500'=>array (2400, 3200, 3700, 5400),
			'1750'=>array (2700, 3600, 4150, 6200),
			'2000'=>array (3000, 4000, 4600, 7000),
			'2500'=>array (3500, 4700, 5400, 8500),
			'3000'=>array (4000, 5400, 6200, 10000),
			'3500'=>array (4500, 6100, 7000, 11500),
			'4000'=>array (5000, 6800, 7800, 13000),
			'4500'=>array (5500, 7500, 8600, 14500),
			'5000'=>array (6000, 8200, 9400, 16000),
			'5500'=>array (6500, 8900, 10200, 17500),
			'6000'=>array (7000, 9600, 11000, 19000),
			'7000'=>array (7800, 10700, 12300, 21100),
			'8000'=>array (8600, 11800, 13600, 23200),
			'9000'=>array (9400, 12900, 14900, 25300),
			'10000'=>array (10200, 14000, 16200, 27400),
			'11000'=>array (11000, 15100, 17500, 29500),
			'12000'=>array (11800, 16200, 18800, 31600),
			'13000'=>array (12600, 17300, 20100, 33700),
			'14000'=>array (13400, 18400, 21400, 35800),
			'15000'=>array (14200, 19500, 22700, 37900),
			'16000'=>array (15000, 20600, 24000, 40000),
			'17000'=>array (15800, 21700, 25300, 42100),
			'18000'=>array (16600, 22800, 26600, 44200),
			'19000'=>array (17400, 23900, 27900, 46300),
			'20000'=>array (18200, 25000, 29200, 48400),
			'21000'=>array (19000, 26100, 30500, 50500),
			'22000'=>array (19800, 27200, 31800, 52600),
			'23000'=>array (20600, 28300, 33100, 54700),
			'24000'=>array (21400, 29400, 34400, 56800),
			'25000'=>array (22200, 30500, 35700, 58900),
			'26000'=>array (23000, 31600, 37000, 61000),
			'27000'=>array (23800, 32700, 38300, 63100),
			'28000'=>array (24600, 33800, 39600, 65200),
			'29000'=>array (25400, 34900, 40900, 67300),
			'30000'=>array (26200, 36000, 42200, 69400)
		);

		// Return the Price List Array
		return $prices;
	}


	/**
	* Airmail Prices List
	*
	* @param $type = Type of Price List ('STD' or 'BONUS')
	* @return array
	*/
	private function _airPrices($type) {
		// Fill the Requested Price List
		switch($type){
		    case ('STD'):
				$prices = array(
					'500'=>array (1700, 2100, 2500, 3200),
					'1000'=>array (2050, 2700, 3350, 4600),
					'1500'=>array (2400, 3300, 4200, 6000),
					'2000'=>array (2750, 3900, 5050, 7400),
					'2500'=>array (3100, 4500, 5900, 8800),
					'3000'=>array (3450, 5100, 6750, 10200),
					'3500'=>array (3800, 5700, 7600, 11600),
					'4000'=>array (4150, 6300, 8450, 13000),
					'4500'=>array (4500, 6900, 9300, 14400),
					'5000'=>array (4850, 7500, 10150, 15800),
					'5500'=>array (5150, 8000, 10900, 17000),
					'6000'=>array (5450, 8500, 11650, 18200),
					'6500'=>array (5750, 9000, 12400, 19400),
					'7000'=>array (6050, 9500, 13150, 20600),
					'7500'=>array (6350, 10000, 13900, 21800),
					'8000'=>array (6650, 10500, 14650, 23000),
					'8500'=>array (6950, 11000, 15400, 24200),
					'9000'=>array (7250, 11500, 16150, 25400),
					'9500'=>array (7550, 12000, 16900, 26600),
					'10000'=>array (7850, 12500, 17650, 27800),
					'11000'=>array (8250, 13200, 18600, 29400),
					'12000'=>array (8650, 13900, 19550, 31000),
					'13000'=>array (9050, 14600, 20500, 32600),
					'14000'=>array (9450, 15300, 21450, 34200),
					'15000'=>array (9850, 16000, 22400, 35800),
					'16000'=>array (10250, 16700, 23350, 37400),
					'17000'=>array (10650, 17400, 24300, 39000),
					'18000'=>array (11050, 18100, 25250, 40600),
					'19000'=>array (11450, 18800, 26200, 42200),
					'20000'=>array (11850, 19500, 27150, 43800),
					'21000'=>array (12250, 20200, 28100, 45400),
					'22000'=>array (12650, 20900, 29050, 47000),
					'23000'=>array (13050, 21600, 30000, 48600),
					'24000'=>array (13450, 22300, 30950, 50200),
					'25000'=>array (13850, 23000, 31900, 51800),
					'26000'=>array (14250, 23700, 32850, 53400),
					'27000'=>array (14650, 24400, 33800, 55000),
					'28000'=>array (15050, 25100, 34750, 56600),
					'29000'=>array (15450, 25800, 35700, 58200),
					'30000'=>array (15850, 26500, 36650, 59800)
				);
		    break;
		    case ('BONUS'):
				$prices = array(
					'50'=>array (120, 150, 170),
					'100'=>array (190, 240, 290),
					'150'=>array (260, 330, 410),
					'200'=>array (330, 420, 530),
					'250'=>array (400, 510, 650),
					'300'=>array (470, 600, 770),
					'350'=>array (540, 690, 890),
					'400'=>array (610, 780, 1010),
					'450'=>array (680, 870, 1130),
					'500'=>array (750, 960, 1250),
					'550'=>array (820, 1050, 1370),
					'600'=>array (890, 1140, 1490),
					'650'=>array (960, 1230, 1610),
					'700'=>array (1030, 1320, 1730),
					'750'=>array (1100, 1410, 1850),
					'800'=>array (1170, 1500, 1970),
					'850'=>array (1240, 1590, 2090),
					'900'=>array (1310, 1680, 2210),
					'950'=>array (1380, 1770, 2330),
					'1000'=>array (1450, 1860, 2450),
					'1250'=>array (1625, 2085, 2750),
					'1500'=>array (1800, 2310, 3050),
					'1750'=>array (1975, 2535, 3350),
					'2000'=>array (2150, 2760, 3650)
				);
		    break;
		}
		// Return the Price List Array
		return $prices;
	}


	/**
	* SAL Prices List
	*
	* @param $type = Type of Price List ('STD' or 'BONUS')
	* @return array
	*/
	private function _salPrices($type) {
		// Fill the Requested Price List
		switch($type){
		    case ('STD'):
				$prices = array(
					'500'=>array (1800, 2200, 2700, 3400),
					'1000'=>array (1800, 2200, 2700, 3400),
					'1500'=>array (2400, 2900, 3850, 5000),
					'2000'=>array (2400, 2900, 3850, 5000),
					'2500'=>array (3000, 3600, 5000, 6600),
					'3000'=>array (3000, 3600, 5000, 6600),
					'3500'=>array (3600, 4300, 6150, 8200),
					'4000'=>array (3600, 4300, 6150, 8200),
					'4500'=>array (4200, 5000, 7300, 9800),
					'5000'=>array (4200, 5000, 7300, 9800),
					'5500'=>array (4700, 5600, 8350, 11250),
					'6000'=>array (4700, 5600, 8350, 11250),
					'6500'=>array (5200, 6200, 9400, 12700),
					'7000'=>array (5200, 6200, 9400, 12700),
					'7500'=>array (5700, 6800, 10450, 14150),
					'8000'=>array (5700, 6800, 10450, 14150),
					'8500'=>array (6200, 7400, 11500, 15600),
					'9000'=>array (6200, 7400, 11500, 15600),
					'9500'=>array (6700, 8000, 12550, 17050),
					'10000'=>array (6700, 8000, 12550, 17050),
					'11000'=>array (7000, 8400, 13250, 18050),
					'12000'=>array (7300, 8800, 13950, 19050),
					'13000'=>array (7600, 9200, 14650, 20050),
					'14000'=>array (7900, 9600, 15350, 21050),
					'15000'=>array (8200, 10000, 16050, 22050),
					'16000'=>array (8500, 10400, 16750, 23050),
					'17000'=>array (8800, 10800, 17450, 24050),
					'18000'=>array (9100, 11200, 18150, 25050),
					'19000'=>array (9400, 11600, 18850, 26050),
					'20000'=>array (9700, 12000, 19550, 27050),
					'21000'=>array (10000, 12400, 20250, 28050),
					'22000'=>array (10300, 12800, 20950, 29050),
					'23000'=>array (10600, 13200, 21650, 30050),
					'24000'=>array (10900, 13600, 22350, 31050),
					'25000'=>array (11200, 14000, 23050, 32050),
					'26000'=>array (11500, 14400, 23750, 33050),
					'27000'=>array (11800, 14800, 24450, 34050),
					'28000'=>array (12100, 15200, 25150, 35050),
					'29000'=>array (12400, 15600, 25850, 36050),
					'30000'=>array (12700, 16000, 26550, 37050)
				);
		    break;
		    case ('BONUS'):
				$prices = array(
					'100'=>array (160, 180, 200),
					'200'=>array (240, 280, 320),
					'300'=>array (320, 380, 440),
					'400'=>array (400, 480, 560),
					'500'=>array (480, 580, 680),
					'600'=>array (560, 680, 800),
					'700'=>array (640, 780, 920),
					'800'=>array (720, 880, 1040),
					'900'=>array (800, 980, 1160),
					'1000'=>array (880, 1080, 1280),
					'1100'=>array (960, 1180, 1400),
					'1200'=>array (1040, 1280, 1520),
					'1300'=>array (1120, 1380, 1640),
					'1400'=>array (1200, 1480, 1760),
					'1500'=>array (1280, 1580, 1880),
					'1600'=>array (1360, 1680, 2000),
					'1700'=>array (1440, 1780, 2120),
					'1800'=>array (1520, 1880, 2240),
					'1900'=>array (1600, 1980, 2360),
					'2000'=>array (1680, 2080, 2480)
				);
		    break;
		}
		// Return the Price List Array
		return $prices;
	}


	/**
	* Surface Mail Prices List
	*
	* @param $type = Type of Price List ('STD' or 'BONUS')
	* @return array
	*/
	private function _surPrices($type) {
		// Fill the Requested Price List
		switch($type){
		    case ('STD'):
				$prices = array(
					'500'=>array (1500, 1700, 1800, 2200),
					'1000'=>array (1500, 1700, 1800, 2200),
					'1500'=>array (1750, 2100, 2350, 2650),
					'2000'=>array (1750, 2100, 2350, 2650),
					'2500'=>array (2000, 2500, 2900, 3100),
					'3000'=>array (2000, 2500, 2900, 3100),
					'3500'=>array (2250, 2900, 3450, 3550),
					'4000'=>array (2250, 2900, 3450, 3550),
					'4500'=>array (2500, 3300, 4000, 4000),
					'5000'=>array (2500, 3300, 4000, 4000),
					'5500'=>array (2750, 3700, 4550, 4450),
					'6000'=>array (2750, 3700, 4550, 4450),
					'6500'=>array (3000, 4100, 5100, 4900),
					'7000'=>array (3000, 4100, 5100, 4900),
					'7500'=>array (3250, 4500, 5650, 5350),
					'8000'=>array (3250, 4500, 5650, 5350),
					'8500'=>array (3500, 4900, 6200, 5800),
					'9000'=>array (3500, 4900, 6200, 5800),
					'9500'=>array (3750, 5300, 6750, 6250),
					'10000'=>array (3750, 5300, 6750, 6250),
					'11000'=>array (3950, 5600, 7100, 6600),
					'12000'=>array (4150, 5900, 7450, 6950),
					'13000'=>array (4350, 6200, 7800, 7300),
					'14000'=>array (4550, 6500, 8150, 7650),
					'15000'=>array (4750, 6800, 8500, 8000),
					'16000'=>array (4950, 7100, 8850, 8350),
					'17000'=>array (5150, 7400, 9200, 8700),
					'18000'=>array (5350, 7700, 9550, 9050),
					'19000'=>array (5550, 8000, 9900, 9400),
					'20000'=>array (5750, 8300, 10250, 9750),
					'21000'=>array (5950, 8600, 10600, 10100),
					'22000'=>array (6150, 8900, 10950, 10450),
					'23000'=>array (6350, 9200, 11300, 10800),
					'24000'=>array (6550, 9500, 11650, 11150),
					'25000'=>array (6750, 9800, 12000, 11500),
					'26000'=>array (6950, 10100, 12350, 11850),
					'27000'=>array (7150, 10400, 12700, 12200),
					'28000'=>array (7350, 10700, 13050, 12550),
					'29000'=>array (7550, 11000, 13400, 12900),
					'30000'=>array (7750, 11300, 13750, 13250)
				);
		    break;
		    case ('BONUS'):
				$prices = array(
					'100'=>array (130),
					'250'=>array (220),
					'500'=>array (430),
					'1000'=>array (770),
					'2000'=>array (1080)
				);
		    break;
		}
		// Return the Price List Array
		return $prices;
	}


	/**
	* Country Shipping Information
	*
	* @param $dstCountryId = Coutry ID for the requested Data
	* @return array
	*/
	public function CountryData($dstCountryId) {

		$countryInfo = array(
'AF'=>array(30000, 'NONE', 'Zone 2', 'NONE', 'Zone 2', 'NONE', 'Zone 2', 'NONE', 'Zone 2', 'NONE', 'Zone 2', 'NONE', 'Zone 2'),
'AL'=>array(20000, 'NONE', 'Zone 3', 'NONE', 'Zone 3', 'NONE', 'Zone 3', 'NONE', 'Zone 3', 'NONE', 'NONE', 'NONE', 'NONE'),
'DZ'=>array(20000, 'Zone 3', 'Zone 4', 'Zone 4', 'Zone 4', 'Zone 3', 'Zone 4', 'Zone 4', 'Zone 4', 'NONE', 'NONE', 'NONE', 'NONE'),
'AS'=>array(30000, 'NONE', 'Zone 3', 'Zone 3', 'Zone 3', 'NONE', 'Zone 3', 'Zone 3', 'Zone 3', 'NONE', 'Zone 3', 'Zone 3', 'Zone 3'),
'AD'=>array(30000, 'Zone 2-2', 'Zone 3', 'Zone 3', 'Zone 3', 'Zone 2-2', 'Zone 3', 'Zone 3', 'Zone 3', 'Zone 2-2', 'Zone 3', 'Zone 3', 'Zone 3'),
'AO'=>array(10000, 'NONE', 'Zone 4', 'NONE', 'Zone 4', 'NONE', 'NONE', 'NONE', 'NONE', 'NONE', 'NONE', 'NONE', 'NONE'),
'AI'=>array(20000, 'NONE', 'Zone 3', 'NONE', 'Zone 3', 'NONE', 'Zone 3', 'NONE', 'Zone 3', 'NONE', 'NONE', 'NONE', 'NONE'),
'AG'=>array(10000, 'NONE', 'Zone 3', 'NONE', 'Zone 3', 'NONE', 'NONE', 'NONE', 'NONE', 'NONE', 'NONE', 'NONE', 'NONE'),
'AR'=>array(20000, 'Zone 3', 'Zone 4', 'Zone 4', 'Zone 4', 'Zone 3', 'Zone 4', 'Zone 4', 'Zone 4', 'NONE', 'NONE', 'NONE', 'NONE'),
'AM'=>array(20000, 'NONE', 'Zone 3', 'Zone 3', 'Zone 3', 'NONE', 'Zone 3', 'Zone 3', 'Zone 3', 'NONE', 'NONE', 'NONE', 'NONE'),
'AW'=>array(30000, 'NONE', 'Zone 3', 'NONE', 'Zone 3', 'NONE', 'Zone 3', 'NONE', 'Zone 3', 'NONE', 'Zone 3', 'NONE', 'Zone 3'),
'AU'=>array(30000, 'Zone 2-1', 'Zone 3', 'Zone 3', 'Zone 3', 'Zone 2-1', 'Zone 3', 'Zone 3', 'Zone 3', 'NONE', 'Zone 3', 'Zone 3', 'Zone 3'),
'AT'=>array(30000, 'Zone 2-2', 'Zone 3', 'Zone 3', 'Zone 3', 'Zone 2-2', 'Zone 3', 'Zone 3', 'Zone 3', 'Zone 2-2', 'Zone 3', 'Zone 3', 'Zone 3'),
'AZ'=>array(30000, 'Zone 2-2', 'Zone 3', 'Zone 3', 'Zone 3', 'Zone 2-2', 'Zone 3', 'Zone 3', 'Zone 3', 'Zone 2-2', 'Zone 3', 'Zone 3', 'Zone 3'),
'BS'=>array(20000, 'NONE', 'Zone 3', 'NONE', 'Zone 3', 'NONE', 'Zone 3', 'NONE', 'Zone 3', 'NONE', 'NONE', 'NONE', 'NONE'),
'BH'=>array(30000, 'Zone 2-1', 'Zone 3', 'Zone 3', 'Zone 3', 'Zone 2-1', 'Zone 3', 'Zone 3', 'Zone 3', 'NONE', 'Zone 3', 'Zone 3', 'Zone 3'),
'BD'=>array(20000, 'Zone 1', 'Zone 2', 'Zone 2', 'Zone 2', 'Zone 1', 'Zone 2', 'Zone 2', 'Zone 2', 'NONE', 'NONE', 'NONE', 'NONE'),
'BB'=>array(30000, 'Zone 2-1', 'Zone 3', 'NONE', 'Zone 3', 'Zone 2-1', 'Zone 3', 'NONE', 'Zone 3', 'Zone 2-1', 'Zone 3', 'NONE', 'Zone 3'),
'BY'=>array(30000, 'Zone 2-2', 'Zone 3', 'Zone 3', 'Zone 3', 'Zone 2-2', 'Zone 3', 'Zone 3', 'Zone 3', 'Zone 2-2', 'Zone 3', 'Zone 3', 'Zone 3'),
'BE'=>array(30000, 'Zone 2-2', 'Zone 3', 'Zone 3', 'Zone 3', 'Zone 2-2', 'Zone 3', 'Zone 3', 'Zone 3', 'Zone 2-2', 'Zone 3', 'Zone 3', 'Zone 3'),
'BZ'=>array(20000, 'NONE', 'Zone 3', 'NONE', 'Zone 3', 'NONE', 'Zone 3', 'NONE', 'Zone 3', 'NONE', 'NONE', 'NONE', 'NONE'),
'BJ'=>array(20000, 'NONE', 'Zone 4', 'NONE', 'Zone 4', 'NONE', 'Zone 4', 'NONE', 'Zone 4', 'NONE', 'NONE', 'NONE', 'NONE'),
'BM'=>array(20000, 'NONE', 'Zone 3', 'NONE', 'Zone 3', 'NONE', 'Zone 3', 'NONE', 'Zone 3', 'NONE', 'NONE', 'NONE', 'NONE'),
'BT'=>array(30000, 'Zone 1', 'Zone 2', 'NONE', 'Zone 2', 'Zone 1', 'Zone 2', 'NONE', 'Zone 2', 'NONE', 'Zone 2', 'NONE', 'NONE'),
'BO'=>array(20000, 'NONE', 'Zone 4', 'NONE', 'Zone 4', 'NONE', 'Zone 4', 'NONE', 'Zone 4', 'NONE', 'NONE', 'NONE', 'NONE'),
'BA'=>array(20000, 'NONE', 'Zone 3', 'NONE', 'Zone 3', 'NONE', 'Zone 3', 'NONE', 'Zone 3', 'NONE', 'NONE', 'NONE', 'NONE'),
'BW'=>array(30000, 'Zone 3', 'Zone 4', 'NONE', 'Zone 4', 'Zone 3', 'Zone 4', 'NONE', 'Zone 4', 'NONE', 'Zone 4', 'NONE', 'Zone 4'),
'BR'=>array(30000, 'Zone 3', 'Zone 4', 'Zone 4', 'Zone 4', 'Zone 3', 'Zone 4', 'Zone 4', 'Zone 4', 'Zone 3', 'Zone 4', 'Zone 4', 'Zone 4'),
'VG'=>array(20000, 'NONE', 'Zone 3', 'NONE', 'Zone 3', 'NONE', 'Zone 3', 'NONE', 'Zone 3', 'NONE', 'NONE', 'NONE', 'NONE'),
'BN'=>array(30000, 'Zone 1', 'Zone 2', 'NONE', 'Zone 2', 'Zone 1', 'Zone 2', 'NONE', 'Zone 2', 'Zone 1', 'Zone 2', 'NONE', 'Zone 2'),
'BG'=>array(30000, 'Zone 2-2', 'Zone 3', 'NONE', 'Zone 3', 'Zone 2-2', 'Zone 3', 'NONE', 'Zone 3', 'NONE', 'Zone 3', 'NONE', 'NONE'),
'BF'=>array(30000, 'NONE', 'Zone 4', 'NONE', 'Zone 4', 'NONE', 'Zone 4', 'NONE', 'Zone 4', 'NONE', 'Zone 4', 'NONE', 'Zone 4'),
'BI'=>array(30000, 'NONE', 'Zone 4', 'NONE', 'Zone 4', 'NONE', 'Zone 4', 'NONE', 'Zone 4', 'NONE', 'Zone 4', 'NONE', 'Zone 4'),
'KH'=>array(30000, 'Zone 1', 'Zone 2', 'NONE', 'Zone 2', 'Zone 1', 'Zone 2', 'NONE', 'Zone 2', 'Zone 1', 'Zone 2', 'NONE', 'Zone 2'),
'CM'=>array(30000, 'NONE', 'Zone 4', 'NONE', 'Zone 4', 'NONE', 'Zone 4', 'NONE', 'Zone 4', 'NONE', 'Zone 4', 'NONE', 'Zone 4'),
'CA'=>array(30000, 'Zone 2-1', 'Zone 3', 'Zone 3', 'Zone 3', 'Zone 2-1', 'Zone 3', 'Zone 3', 'Zone 3', 'Zone 2-1', 'Zone 3', 'Zone 3', 'Zone 3'),
'CV'=>array(30000, 'NONE', 'Zone 4', 'NONE', 'Zone 4', 'NONE', 'Zone 4', 'NONE', 'Zone 4', 'NONE', 'NONE', 'NONE', 'Zone 4'),
'KY'=>array(20000, 'NONE', 'Zone 3', 'NONE', 'Zone 3', 'NONE', 'Zone 3', 'NONE', 'Zone 3', 'NONE', 'NONE', 'NONE', 'NONE'),
'CF'=>array(30000, 'NONE', 'Zone 4', 'NONE', 'Zone 4', 'NONE', 'Zone 4', 'NONE', 'Zone 4', 'NONE', 'Zone 4', 'NONE', 'Zone 4'),
'TD'=>array(30000, 'NONE', 'Zone 4', 'NONE', 'Zone 4', 'NONE', 'Zone 4', 'NONE', 'Zone 4', 'NONE', 'Zone 4', 'NONE', 'Zone 4'),
'CL'=>array(30000, 'Zone 3', 'Zone 4', 'Zone 4', 'Zone 4', 'Zone 3', 'Zone 4', 'Zone 4', 'Zone 4', 'Zone 3', 'Zone 4', 'Zone 4', 'Zone 4'),
'CN'=>array(30000, 'Zone 1', 'Zone 1', 'Zone 1', 'Zone 1', 'Zone 1', 'Zone 1', 'Zone 1', 'Zone 1', 'Zone 1', 'Zone 1', 'Zone 1', 'Zone 1'),
'CX'=>array(20000, 'NONE', 'Zone 3', 'NONE', 'Zone 3', 'NONE', 'Zone 3', 'NONE', 'Zone 3', 'NONE', 'NONE', 'NONE', 'NONE'),
'CC'=>array(20000, 'NONE', 'Zone 3', 'NONE', 'Zone 3', 'NONE', 'Zone 3', 'NONE', 'Zone 3', 'NONE', 'NONE', 'NONE', 'NONE'),
'CO'=>array(30000, 'Zone 3', 'Zone 4', 'Zone 4', 'Zone 4', 'Zone 3', 'Zone 4', 'Zone 4', 'Zone 4', 'Zone 3', 'Zone 4', 'Zone 4', 'Zone 4'),
'KM'=>array(20000, 'NONE', 'Zone 4', 'NONE', 'Zone 4', 'NONE', 'Zone 4', 'NONE', 'Zone 4', 'NONE', 'NONE', 'NONE', 'NONE'),
'CG'=>array(20000, 'NONE', 'Zone 4', 'NONE', 'Zone 4', 'NONE', 'Zone 4', 'NONE', 'Zone 4', 'NONE', 'NONE', 'NONE', 'NONE'),
'CD'=>array(30000, 'NONE', 'Zone 4', 'NONE', 'Zone 4', 'NONE', 'Zone 4', 'NONE', 'Zone 4', 'NONE', 'Zone 4', 'NONE', 'Zone 4'),
'CK'=>array(20000, 'Zone 2-1', 'Zone 3', 'NONE', 'Zone 3', 'Zone 2-1', 'Zone 3', 'NONE', 'Zone 3', 'NONE', 'NONE', 'NONE', 'NONE'),
'CR'=>array(30000, 'Zone 2-1', 'Zone 3', 'NONE', 'Zone 3', 'Zone 2-1', 'Zone 3', 'NONE', 'Zone 3', 'NONE', 'Zone 3', 'NONE', 'Zone 3'),
'CI'=>array(30000, 'Zone 3', 'Zone 4', 'NONE', 'Zone 4', 'Zone 3', 'Zone 4', 'NONE', 'Zone 4', 'Zone 3', 'Zone 4', 'NONE', 'Zone 4'),
'HR'=>array(30000, 'Zone 2-2', 'Zone 3', 'Zone 3', 'Zone 3', 'Zone 2-2', 'Zone 3', 'Zone 3', 'Zone 3', 'Zone 2-2', 'Zone 3', 'Zone 3', 'Zone 3'),
'CU'=>array(20000, 'Zone 2-1', 'Zone 3', 'NONE', 'Zone 3', 'Zone 2-1', 'NONE', 'NONE', 'NONE', 'NONE', 'NONE', 'NONE', 'NONE'),
'CY'=>array(30000, 'Zone 2-1', 'Zone 3', 'NONE', 'Zone 3', 'Zone 2-1', 'Zone 3', 'NONE', 'Zone 3', 'Zone 2-1', 'Zone 3', 'NONE', 'Zone 3'),
'CZ'=>array(30000, 'Zone 2-2', 'Zone 3', 'Zone 3', 'Zone 3', 'Zone 2-2', 'Zone 3', 'Zone 3', 'Zone 3', 'Zone 2-2', 'Zone 3', 'Zone 3', 'Zone 3'),
'DK'=>array(30000, 'Zone 2-2', 'Zone 3', 'Zone 3', 'Zone 3', 'Zone 2-2', 'Zone 3', 'Zone 3', 'Zone 3', 'Zone 2-2', 'Zone 3', 'Zone 3', 'Zone 3'),
'DJ'=>array(30000, 'Zone 3', 'Zone 4', 'NONE', 'Zone 4', 'Zone 3', 'Zone 4', 'NONE', 'Zone 4', 'NONE', 'NONE', 'NONE', 'Zone 4'),
'DM'=>array(20000, 'NONE', 'Zone 3', 'NONE', 'Zone 3', 'NONE', 'Zone 3', 'NONE', 'Zone 3', 'NONE', 'NONE', 'NONE', 'NONE'),
'DO'=>array(30000, 'NONE', 'Zone 3', 'NONE', 'Zone 3', 'NONE', 'Zone 3', 'NONE', 'Zone 3', 'NONE', 'Zone 3', 'NONE', 'Zone 3'),
'EC'=>array(30000, 'Zone 3', 'Zone 4', 'NONE', 'Zone 4', 'Zone 3', 'Zone 4', 'NONE', 'Zone 4', 'Zone 3', 'Zone 4', 'NONE', 'Zone 4'),
'EG'=>array(30000, 'Zone 3', 'Zone 4', 'Zone 4', 'Zone 4', 'Zone 3', 'Zone 4', 'Zone 4', 'Zone 4', 'NONE', 'Zone 4', 'Zone 4', 'Zone 4'),
'SV'=>array(20000, 'Zone 2-1', 'Zone 3', 'NONE', 'Zone 3', 'Zone 2-1', 'Zone 3', 'NONE', 'Zone 3', 'NONE', 'NONE', 'NONE', 'NONE'),
'GQ'=>array(30000, 'NONE', 'Zone 4', 'NONE', 'Zone 4', 'NONE', 'Zone 4', 'NONE', 'Zone 4', 'NONE', 'NONE', 'NONE', 'Zone 4'),
'ER'=>array(20000, 'NONE', 'Zone 4', 'NONE', 'Zone 4', 'NONE', 'Zone 4', 'NONE', 'Zone 4', 'NONE', 'NONE', 'NONE', 'NONE'),
'EE'=>array(30000, 'Zone 2-2', 'Zone 3', 'Zone 3', 'Zone 3', 'Zone 2-2', 'Zone 3', 'Zone 3', 'Zone 3', 'Zone 2-2', 'Zone 3', 'Zone 3', 'Zone 3'),
'ET'=>array(30000, 'Zone 3', 'Zone 4', 'NONE', 'Zone 4', 'Zone 3', 'Zone 4', 'NONE', 'Zone 4', 'Zone 3', 'Zone 4', 'NONE', 'Zone 4'),
'FK'=>array(20000, 'NONE', 'Zone 4', 'NONE', 'Zone 4', 'NONE', 'Zone 4', 'NONE', 'Zone 4', 'NONE', 'NONE', 'NONE', 'NONE'),
'FJ'=>array(20000, 'Zone 2-1', 'Zone 3', 'NONE', 'Zone 3', 'Zone 2-1', 'Zone 3', 'NONE', 'Zone 3', 'NONE', 'NONE', 'NONE', 'NONE'),
'FI'=>array(30000, 'Zone 2-2', 'Zone 3', 'Zone 3', 'Zone 3', 'Zone 2-2', 'Zone 3', 'Zone 3', 'Zone 3', 'Zone 2-2', 'Zone 3', 'Zone 3', 'Zone 3'),
'FR'=>array(30000, 'Zone 2-2', 'Zone 3', 'Zone 3', 'Zone 3', 'Zone 2-2', 'Zone 3', 'Zone 3', 'Zone 3', 'Zone 2-2', 'Zone 3', 'Zone 3', 'Zone 3'),
'GF'=>array(30000, 'Zone 3', 'Zone 4', 'NONE', 'Zone 4', 'Zone 3', 'Zone 4', 'NONE', 'Zone 4', 'Zone 3', 'Zone 4', 'NONE', 'Zone 4'),
'PF'=>array(30000, 'NONE', 'Zone 3', 'NONE', 'Zone 3', 'NONE', 'Zone 3', 'NONE', 'Zone 3', 'NONE', 'Zone 3', 'NONE', 'Zone 3'),
'GA'=>array(30000, 'Zone 3', 'Zone 4', 'NONE', 'Zone 4', 'Zone 3', 'Zone 4', 'NONE', 'Zone 4', 'NONE', 'Zone 4', 'NONE', 'Zone 4'),
'GM'=>array(30000, 'NONE', 'Zone 4', 'NONE', 'Zone 4', 'NONE', 'Zone 4', 'NONE', 'Zone 4', 'NONE', 'Zone 4', 'NONE', 'Zone 4'),
'GE'=>array(20000, 'NONE', 'Zone 3', 'Zone 3', 'Zone 3', 'NONE', 'Zone 3', 'Zone 3', 'Zone 3', 'NONE', 'NONE', 'NONE', 'NONE'),
'DE'=>array(30000, 'Zone 2-2', 'Zone 3', 'Zone 3', 'Zone 3', 'Zone 2-2', 'Zone 3', 'Zone 3', 'Zone 3', 'Zone 2-2', 'Zone 3', 'Zone 3', 'Zone 3'),
'GH'=>array(30000, 'Zone 3', 'Zone 4', 'NONE', 'Zone 4', 'Zone 3', 'Zone 4', 'NONE', 'Zone 4', 'Zone 3', 'Zone 4', 'NONE', 'Zone 4'),
'GI'=>array(20000, 'NONE', 'Zone 3', 'Zone 3', 'Zone 3', 'NONE', 'Zone 3', 'Zone 3', 'Zone 3', 'NONE', 'NONE', 'NONE', 'NONE'),
'GR'=>array(30000, 'Zone 2-2', 'Zone 3', 'Zone 3', 'Zone 3', 'Zone 2-2', 'Zone 3', 'Zone 3', 'Zone 3', 'Zone 2-2', 'NONE', 'NONE', 'NONE'),
'GL'=>array(30000, 'NONE', 'Zone 3', 'Zone 3', 'Zone 3', 'NONE', 'Zone 3', 'Zone 3', 'Zone 3', 'NONE', 'Zone 3', 'Zone 3', 'Zone 3'),
'GD'=>array(20000, 'NONE', 'Zone 3', 'NONE', 'Zone 3', 'NONE', 'Zone 3', 'NONE', 'Zone 3', 'NONE', 'NONE', 'NONE', 'NONE'),
'GP'=>array(30000, 'Zone 2-1', 'Zone 3', 'NONE', 'Zone 3', 'Zone 2-1', 'Zone 3', 'NONE', 'Zone 3', 'Zone 2-1', 'Zone 3', 'NONE', 'Zone 3'),
'GU'=>array(30000, 'Zone 1', 'Zone 1', 'Zone 1', 'Zone 1', 'Zone 1', 'Zone 1', 'Zone 1', 'Zone 1', 'Zone 1', 'Zone 1', 'Zone 1', 'Zone 1'),
'GT'=>array(30000, 'NONE', 'Zone 3', 'NONE', 'Zone 3', 'NONE', 'Zone 3', 'NONE', 'Zone 3', 'NONE', 'Zone 3', 'NONE', 'Zone 3'),
'GG'=>array(30000, 'Zone 2-2', 'Zone 3', 'Zone 3', 'Zone 3', 'Zone 2-2', 'Zone 3', 'Zone 3', 'Zone 3', 'Zone 2-2', 'Zone 3', 'Zone 3', 'Zone 3'),
'GN'=>array(30000, 'NONE', 'Zone 4', 'NONE', 'Zone 4', 'NONE', 'Zone 4', 'NONE', 'Zone 4', 'NONE', 'Zone 4', 'NONE', 'Zone 4'),
'GW'=>array(10000, 'NONE', 'Zone 4', 'NONE', 'Zone 4', 'NONE', 'NONE', 'NONE', 'NONE', 'NONE', 'NONE', 'NONE', 'NONE'),
'GY'=>array(20000, 'NONE', 'Zone 4', 'NONE', 'Zone 4', 'NONE', 'Zone 4', 'NONE', 'Zone 4', 'NONE', 'NONE', 'NONE', 'NONE'),
'HT'=>array(30000, 'NONE', 'Zone 3', 'NONE', 'Zone 3', 'NONE', 'Zone 3', 'NONE', 'Zone 3', 'NONE', 'Zone 3', 'NONE', 'NONE'),
'HN'=>array(20000, 'Zone 2-1', 'Zone 3', 'NONE', 'Zone 3', 'Zone 2-1', 'Zone 3', 'NONE', 'Zone 3', 'NONE', 'NONE', 'NONE', 'NONE'),
'HK'=>array(30000, 'Zone 1', 'Zone 1', 'Zone 1', 'Zone 1', 'Zone 1', 'Zone 1', 'Zone 1', 'Zone 1', 'Zone 1', 'Zone 1', 'Zone 1', 'Zone 1'),
'HU'=>array(30000, 'Zone 2-2', 'Zone 3', 'Zone 3', 'Zone 3', 'Zone 2-2', 'Zone 3', 'Zone 3', 'Zone 3', 'Zone 2-2', 'NONE', 'NONE', 'NONE'),
'IS'=>array(30000, 'Zone 2-2', 'Zone 3', 'Zone 3', 'Zone 3', 'Zone 2-2', 'Zone 3', 'Zone 3', 'Zone 3', 'Zone 2-2', 'Zone 3', 'Zone 3', 'Zone 3'),
'IN'=>array(30000, 'Zone 1', 'Zone 2', 'Zone 2', 'Zone 2', 'Zone 1', 'Zone 2', 'Zone 2', 'Zone 2', 'Zone 1', 'NONE', 'NONE', 'NONE'),
'ID'=>array(30000, 'Zone 1', 'Zone 2', 'Zone 2', 'Zone 2', 'Zone 1', 'Zone 2', 'Zone 2', 'Zone 2', 'Zone 1', 'Zone 2', 'Zone 2', 'Zone 2'),
'IR'=>array(30000, 'Zone 2-1', 'Zone 3', 'NONE', 'Zone 3', 'Zone 2-1', 'Zone 3', 'NONE', 'Zone 3', 'NONE', 'NONE', 'NONE', 'Zone 3'),
'IQ'=>array(30000, 'Zone 2-1', 'Zone 3', 'NONE', 'Zone 3', 'Zone 2-1', 'Zone 3', 'NONE', 'Zone 3', 'NONE', 'Zone 3', 'NONE', 'Zone 3'),
'IE'=>array(30000, 'Zone 2-2', 'Zone 3', 'Zone 3', 'Zone 3', 'Zone 2-2', 'Zone 3', 'Zone 3', 'Zone 3', 'Zone 2-2', 'Zone 3', 'Zone 3', 'Zone 3'),
'IM'=>array(30000, 'Zone 2-2', 'Zone 3', 'Zone 3', 'Zone 3', 'Zone 2-2', 'Zone 3', 'Zone 3', 'Zone 3', 'Zone 2-2', 'Zone 3', 'Zone 3', 'Zone 3'),
'IL'=>array(20000, 'Zone 2-1', 'Zone 3', 'Zone 3', 'Zone 3', 'Zone 2-1', 'Zone 3', 'Zone 3', 'Zone 3', 'NONE', 'NONE', 'NONE', 'NONE'),
'IT'=>array(30000, 'Zone 2-2', 'Zone 3', 'Zone 3', 'Zone 3', 'Zone 2-2', 'Zone 3', 'Zone 3', 'Zone 3', 'Zone 2-2', 'Zone 3', 'Zone 3', 'Zone 3'),
'JM'=>array(30000, 'Zone 2-1', 'Zone 3', 'NONE', 'Zone 3', 'Zone 2-1', 'Zone 3', 'NONE', 'Zone 3', 'Zone 2-1', 'NONE', 'NONE', 'NONE'),
'JE'=>array(30000, 'Zone 2-2', 'Zone 3', 'Zone 3', 'Zone 3', 'Zone 2-2', 'Zone 3', 'Zone 3', 'Zone 3', 'Zone 2-2', 'Zone 3', 'Zone 3', 'Zone 3'),
'JO'=>array(30000, 'Zone 2-1', 'Zone 3', 'NONE', 'Zone 3', 'Zone 2-1', 'Zone 3', 'NONE', 'Zone 3', 'Zone 2-1', 'Zone 3', 'NONE', 'Zone 3'),
'KZ'=>array(20000, 'NONE', 'Zone 3', 'Zone 3', 'Zone 3', 'NONE', 'Zone 3', 'Zone 3', 'Zone 3', 'NONE', 'NONE', 'NONE', 'NONE'),
'KE'=>array(30000, 'Zone 3', 'Zone 4', 'Zone 4', 'Zone 4', 'Zone 3', 'Zone 4', 'Zone 4', 'Zone 4', 'Zone 3', 'Zone 4', 'Zone 4', 'Zone 4'),
'KI'=>array(20000, 'NONE', 'Zone 3', 'NONE', 'Zone 3', 'NONE', 'Zone 3', 'NONE', 'Zone 3', 'NONE', 'NONE', 'NONE', 'NONE'),
'KW'=>array(30000, 'Zone 2-1', 'Zone 3', 'Zone 3', 'Zone 3', 'Zone 2-1', 'Zone 3', 'Zone 3', 'Zone 3', 'Zone 2-1', 'Zone 3', 'Zone 3', 'Zone 3'),
'KG'=>array(20000, 'NONE', 'Zone 3', 'Zone 3', 'Zone 3', 'NONE', 'Zone 3', 'Zone 3', 'Zone 3', 'NONE', 'NONE', 'NONE', 'NONE'),
'LA'=>array(30000, 'Zone 1', 'Zone 2', 'NONE', 'Zone 2', 'Zone 1', 'Zone 2', 'NONE', 'Zone 2', 'NONE', 'Zone 2', 'NONE', 'Zone 2'),
'LV'=>array(30000, 'Zone 2-2', 'Zone 3', 'Zone 3', 'Zone 3', 'Zone 2-2', 'Zone 3', 'Zone 3', 'Zone 3', 'Zone 2-2', 'Zone 3', 'Zone 3', 'Zone 3'),
'LB'=>array(30000, 'NONE', 'Zone 3', 'NONE', 'Zone 3', 'NONE', 'Zone 3', 'NONE', 'Zone 3', 'NONE', 'Zone 3', 'NONE', 'Zone 3'),
'LS'=>array(10000, 'NONE', 'Zone 4', 'NONE', 'Zone 4', 'NONE', 'NONE', 'NONE', 'NONE', 'NONE', 'NONE', 'NONE', 'NONE'),
'LR'=>array(30000, 'NONE', 'Zone 4', 'NONE', 'Zone 4', 'NONE', 'Zone 4', 'NONE', 'Zone 4', 'NONE', 'NONE', 'NONE', 'Zone 4'),
'LY'=>array(20000, 'NONE', 'Zone 4', 'NONE', 'Zone 4', 'NONE', 'Zone 4', 'NONE', 'Zone 4', 'NONE', 'NONE', 'NONE', 'NONE'),
'LI'=>array(30000, 'Zone 2-2', 'Zone 3', 'Zone 3', 'Zone 3', 'Zone 2-2', 'Zone 3', 'Zone 3', 'Zone 3', 'Zone 2-2', 'NONE', 'NONE', 'NONE'),
'LT'=>array(30000, 'Zone 2-2', 'Zone 3', 'Zone 3', 'Zone 3', 'Zone 2-2', 'Zone 3', 'Zone 3', 'Zone 3', 'NONE', 'Zone 3', 'Zone 3', 'Zone 3'),
'LU'=>array(30000, 'Zone 2-2', 'Zone 3', 'Zone 3', 'Zone 3', 'Zone 2-2', 'Zone 3', 'Zone 3', 'Zone 3', 'Zone 2-2', 'Zone 3', 'Zone 3', 'Zone 3'),
'MO'=>array(30000, 'Zone 1', 'Zone 1', 'NONE', 'Zone 1', 'Zone 1', 'Zone 1', 'NONE', 'Zone 1', 'Zone 1', 'Zone 1', 'NONE', 'Zone 1'),
'MK'=>array(30000, 'Zone 2-2', 'Zone 3', 'NONE', 'Zone 3', 'Zone 2-2', 'Zone 3', 'NONE', 'Zone 3', 'Zone 2-2', 'NONE', 'NONE', 'Zone 3'),
'MG'=>array(30000, 'Zone 3', 'Zone 4', 'NONE', 'Zone 4', 'Zone 3', 'Zone 4', 'NONE', 'Zone 4', 'Zone 3', 'Zone 4', 'NONE', 'Zone 4'),
'MW'=>array(30000, 'NONE', 'Zone 4', 'NONE', 'Zone 4', 'NONE', 'Zone 4', 'NONE', 'Zone 4', 'NONE', 'Zone 4', 'NONE', 'Zone 4'),
'MY'=>array(30000, 'Zone 1', 'Zone 2', 'Zone 2', 'Zone 2', 'Zone 1', 'Zone 2', 'Zone 2', 'Zone 2', 'Zone 1', 'Zone 2', 'Zone 2', 'Zone 2'),
'MV'=>array(30000, 'Zone 1', 'Zone 2', 'NONE', 'Zone 2', 'Zone 1', 'Zone 2', 'NONE', 'Zone 2', 'NONE', 'Zone 2', 'NONE', 'Zone 2'),
'ML'=>array(30000, 'NONE', 'Zone 4', 'NONE', 'Zone 4', 'NONE', 'Zone 4', 'NONE', 'Zone 4', 'NONE', 'Zone 4', 'NONE', 'Zone 4'),
'MT'=>array(30000, 'Zone 2-2', 'Zone 3', 'Zone 3', 'Zone 3', 'Zone 2-2', 'Zone 3', 'Zone 3', 'Zone 3', 'NONE', 'Zone 3', 'Zone 3', 'Zone 3'),
'MH'=>array(20000, 'NONE', 'Zone 1', 'Zone 1', 'Zone 1', 'NONE', 'Zone 1', 'Zone 1', 'Zone 1', 'NONE', 'NONE', 'NONE', 'NONE'),
'MQ'=>array(30000, 'Zone 2-1', 'Zone 3', 'NONE', 'Zone 3', 'Zone 2-1', 'Zone 3', 'NONE', 'Zone 3', 'Zone 2-1', 'Zone 3', 'NONE', 'Zone 3'),
'MR'=>array(20000, 'NONE', 'Zone 4', 'NONE', 'Zone 4', 'NONE', 'Zone 4', 'NONE', 'Zone 4', 'NONE', 'NONE', 'NONE', 'NONE'),
'MU'=>array(30000, 'Zone 3', 'Zone 4', 'NONE', 'Zone 4', 'Zone 3', 'Zone 4', 'NONE', 'Zone 4', 'NONE', 'Zone 4', 'NONE', 'Zone 4'),
'MX'=>array(30000, 'Zone 2-1', 'Zone 3', 'Zone 3', 'Zone 3', 'Zone 2-1', 'Zone 3', 'Zone 3', 'Zone 3', 'NONE', 'Zone 3', 'Zone 3', 'Zone 3'),
'FM'=>array(20000, 'NONE', 'Zone 1', 'Zone 1', 'Zone 1', 'NONE', 'Zone 1', 'Zone 1', 'Zone 1', 'NONE', 'NONE', 'NONE', 'NONE'),
'MD'=>array(30000, 'NONE', 'Zone 3', 'Zone 3', 'Zone 3', 'NONE', 'Zone 3', 'Zone 3', 'Zone 3', 'NONE', 'Zone 3', 'Zone 3', 'Zone 3'),
'MC'=>array(30000, 'Zone 2-2', 'Zone 3', 'Zone 3', 'Zone 3', 'Zone 2-2', 'Zone 3', 'Zone 3', 'Zone 3', 'Zone 2-2', 'Zone 3', 'Zone 3', 'Zone 3'),
'MN'=>array(30000, 'Zone 1', 'Zone 1', 'NONE', 'Zone 1', 'Zone 1', 'Zone 1', 'NONE', 'Zone 1', 'Zone 1', 'Zone 1', 'NONE', 'Zone 1'),
'ME'=>array(30000, 'NONE', 'Zone 3', 'Zone 3', 'Zone 3', 'NONE', 'Zone 3', 'Zone 3', 'Zone 3', 'NONE', 'Zone 3', 'Zone 3', 'Zone 3'),
'MS'=>array(20000, 'NONE', 'Zone 3', 'NONE', 'Zone 3', 'NONE', 'Zone 3', 'NONE', 'Zone 3', 'NONE', 'NONE', 'NONE', 'NONE'),
'MA'=>array(30000, 'Zone 3', 'Zone 4', 'NONE', 'Zone 4', 'Zone 3', 'Zone 4', 'NONE', 'Zone 4', 'Zone 3', 'Zone 4', 'NONE', 'Zone 4'),
'MZ'=>array(20000, 'NONE', 'Zone 4', 'NONE', 'Zone 4', 'NONE', 'Zone 4', 'NONE', 'Zone 4', 'NONE', 'NONE', 'NONE', 'NONE'),
'MM'=>array(20000, 'Zone 1', 'Zone 2', 'NONE', 'Zone 2', 'Zone 1', 'Zone 2', 'NONE', 'Zone 2', 'NONE', 'NONE', 'NONE', 'NONE'),
'NA'=>array(10000, 'NONE', 'Zone 4', 'NONE', 'Zone 4', 'NONE', 'NONE', 'NONE', 'NONE', 'NONE', 'NONE', 'NONE', 'NONE'),
'NR'=>array(30000, 'NONE', 'Zone 3', 'NONE', 'Zone 3', 'NONE', 'Zone 3', 'NONE', 'Zone 3', 'NONE', 'NONE', 'NONE', 'Zone 3'),
'NP'=>array(30000, 'Zone 1', 'Zone 2', 'NONE', 'Zone 2', 'Zone 1', 'Zone 2', 'NONE', 'Zone 2', 'Zone 1', 'NONE', 'NONE', 'NONE'),
'NL'=>array(30000, 'Zone 2-2', 'Zone 3', 'Zone 3', 'Zone 3', 'Zone 2-2', 'Zone 3', 'Zone 3', 'Zone 3', 'Zone 2-2', 'NONE', 'NONE', 'NONE'),
'AN'=>array(30000, 'NONE', 'Zone 3', 'NONE', 'Zone 3', 'NONE', 'Zone 3', 'NONE', 'Zone 3', 'NONE', 'Zone 3', 'NONE', 'Zone 3'),
'NC'=>array(30000, 'Zone 2-1', 'Zone 3', 'NONE', 'Zone 3', 'Zone 2-1', 'Zone 3', 'NONE', 'Zone 3', 'Zone 2-1', 'Zone 3', 'NONE', 'Zone 3'),
'NZ'=>array(30000, 'Zone 2-1', 'Zone 3', 'Zone 3', 'Zone 3', 'Zone 2-1', 'Zone 3', 'Zone 3', 'Zone 3', 'NONE', 'Zone 3', 'Zone 3', 'Zone 3'),
'NI'=>array(30000, 'NONE', 'Zone 3', 'NONE', 'Zone 3', 'NONE', 'Zone 3', 'NONE', 'Zone 3', 'NONE', 'NONE', 'NONE', 'Zone 3'),
'NE'=>array(30000, 'NONE', 'Zone 4', 'NONE', 'Zone 4', 'NONE', 'Zone 4', 'NONE', 'Zone 4', 'NONE', 'Zone 4', 'NONE', 'Zone 4'),
'NG'=>array(30000, 'Zone 3', 'Zone 4', 'NONE', 'Zone 4', 'Zone 3', 'Zone 4', 'NONE', 'Zone 4', 'Zone 3', 'Zone 4', 'NONE', 'Zone 4'),
'NU'=>array(30000, 'NONE', 'Zone 3', 'Zone 3', 'Zone 3', 'NONE', 'Zone 3', 'Zone 3', 'Zone 3', 'NONE', 'Zone 3', 'Zone 3', 'Zone 3'),
'NF'=>array(20000, 'NONE', 'Zone 3', 'NONE', 'Zone 3', 'NONE', 'Zone 3', 'NONE', 'Zone 3', 'NONE', 'NONE', 'NONE', 'NONE'),
'KP'=>array(20000, 'NONE', 'Zone 1', 'NONE', 'Zone 1', 'NONE', 'Zone 1', 'NONE', 'Zone 1', 'NONE', 'NONE', 'NONE', 'NONE'),
'MP'=>array(30000, 'NONE', 'Zone 1', 'Zone 1', 'Zone 1', 'NONE', 'Zone 1', 'Zone 1', 'Zone 1', 'NONE', 'Zone 1', 'Zone 1', 'Zone 1'),
'NO'=>array(30000, 'Zone 2-2', 'Zone 3', 'Zone 3', 'Zone 3', 'Zone 2-2', 'Zone 3', 'Zone 3', 'Zone 3', 'Zone 2-2', 'Zone 3', 'Zone 3', 'Zone 3'),
'OM'=>array(20000, 'Zone 2-1', 'Zone 3', 'NONE', 'Zone 3', 'Zone 2-1', 'Zone 3', 'NONE', 'Zone 3', 'NONE', 'NONE', 'NONE', 'NONE'),
'PK'=>array(30000, 'Zone 1', 'Zone 2', 'Zone 2', 'Zone 2', 'Zone 1', 'Zone 2', 'Zone 2', 'Zone 2', 'Zone 1', 'Zone 2', 'Zone 2', 'Zone 2'),
'PW'=>array(20000, 'NONE', 'Zone 1', 'Zone 1', 'Zone 1', 'NONE', 'Zone 1', 'Zone 1', 'Zone 1', 'NONE', 'NONE', 'NONE', 'NONE'),
'PA'=>array(30000, 'Zone 2-1', 'Zone 3', 'NONE', 'Zone 3', 'Zone 2-1', 'Zone 3', 'NONE', 'Zone 3', 'Zone 2-1', 'Zone 3', 'NONE', 'Zone 3'),
'PG'=>array(30000, 'Zone 2-1', 'Zone 3', 'NONE', 'Zone 3', 'Zone 2-1', 'Zone 3', 'NONE', 'Zone 3', 'Zone 2-1', 'NONE', 'NONE', 'NONE'),
'PY'=>array(20000, 'Zone 3', 'Zone 4', 'NONE', 'Zone 4', 'Zone 3', 'Zone 4', 'NONE', 'Zone 4', 'NONE', 'NONE', 'NONE', 'NONE'),
'PE'=>array(30000, 'Zone 3', 'Zone 4', 'Zone 4', 'Zone 4', 'Zone 3', 'Zone 4', 'Zone 4', 'Zone 4', 'Zone 3', 'Zone 4', 'Zone 4', 'Zone 4'),
'PH'=>array(20000, 'Zone 1', 'Zone 1', 'Zone 1', 'Zone 1', 'Zone 1', 'Zone 1', 'Zone 1', 'Zone 1', 'NONE', 'NONE', 'NONE', 'NONE'),
'PN'=>array(10000, 'NONE', 'Zone 3', 'NONE', 'Zone 3', 'NONE', 'NONE', 'NONE', 'NONE', 'NONE', 'NONE', 'NONE', 'NONE'),
'PL'=>array(20000, 'Zone 2-2', 'Zone 3', 'Zone 3', 'Zone 3', 'Zone 2-2', 'Zone 3', 'Zone 3', 'Zone 3', 'NONE', 'NONE', 'NONE', 'NONE'),
'PT'=>array(30000, 'Zone 2-2', 'Zone 3', 'Zone 3', 'Zone 3', 'Zone 2-2', 'Zone 3', 'Zone 3', 'Zone 3', 'Zone 2-2', 'Zone 3', 'Zone 3', 'Zone 3'),
'PR'=>array(30000, 'Zone 2-1', 'Zone 3', 'Zone 3', 'Zone 3', 'Zone 2-1', 'Zone 3', 'Zone 3', 'Zone 3', 'Zone 2-1', 'Zone 3', 'Zone 3', 'Zone 3'),
'QA'=>array(30000, 'Zone 2-1', 'Zone 3', 'NONE', 'Zone 3', 'Zone 2-1', 'Zone 3', 'NONE', 'Zone 3', 'NONE', 'Zone 3', 'NONE', 'Zone 3'),
'RO'=>array(30000, 'Zone 2-2', 'Zone 3', 'NONE', 'Zone 3', 'Zone 2-2', 'Zone 3', 'NONE', 'Zone 3', 'Zone 2-2', 'Zone 3', 'NONE', 'Zone 3'),
'RU'=>array(30000, 'Zone 2-2', 'Zone 3', 'Zone 3', 'Zone 3', 'Zone 2-2', 'Zone 3', 'Zone 3', 'Zone 3', 'Zone 2-2', 'NONE', 'NONE', 'NONE'),
'RW'=>array(30000, 'Zone 3', 'Zone 4', 'NONE', 'Zone 4', 'Zone 3', 'Zone 4', 'NONE', 'Zone 4', 'Zone 3', 'Zone 4', 'NONE', 'Zone 4'),
'RE'=>array(30000, 'Zone 3', 'Zone 4', 'NONE', 'Zone 4', 'Zone 3', 'Zone 4', 'NONE', 'Zone 4', 'Zone 3', 'Zone 4', 'NONE', 'Zone 4'),
'SH'=>array(30000, 'NONE', 'Zone 4', 'NONE', 'Zone 4', 'NONE', 'Zone 4', 'NONE', 'Zone 4', 'NONE', 'NONE', 'NONE', 'Zone 4'),
'KN'=>array(20000, 'NONE', 'Zone 3', 'NONE', 'Zone 3', 'NONE', 'Zone 3', 'NONE', 'Zone 3', 'NONE', 'NONE', 'NONE', 'NONE'),
'LC'=>array(30000, 'NONE', 'Zone 3', 'NONE', 'Zone 3', 'NONE', 'Zone 3', 'NONE', 'Zone 3', 'NONE', 'Zone 3', 'NONE', 'NONE'),
'VC'=>array(10000, 'NONE', 'Zone 3', 'NONE', 'Zone 3', 'NONE', 'NONE', 'NONE', 'NONE', 'NONE', 'NONE', 'NONE', 'NONE'),
'WS'=>array(20000, 'NONE', 'Zone 3', 'NONE', 'Zone 3', 'NONE', 'Zone 3', 'NONE', 'Zone 3', 'NONE', 'NONE', 'NONE', 'NONE'),
'SM'=>array(30000, 'Zone 2-2', 'Zone 3', 'Zone 3', 'Zone 3', 'Zone 2-2', 'Zone 3', 'Zone 3', 'Zone 3', 'Zone 2-2', 'NONE', 'NONE', 'NONE'),
'SA'=>array(30000, 'Zone 2-1', 'Zone 3', 'Zone 3', 'Zone 3', 'Zone 2-1', 'Zone 3', 'Zone 3', 'Zone 3', 'Zone 2-1', 'Zone 3', 'Zone 3', 'Zone 3'),
'SN'=>array(30000, 'Zone 3', 'Zone 4', 'NONE', 'Zone 4', 'Zone 3', 'Zone 4', 'NONE', 'Zone 4', 'Zone 3', 'Zone 4', 'NONE', 'Zone 4'),
'RS'=>array(30000, 'NONE', 'Zone 3', 'Zone 3', 'Zone 3', 'NONE', 'Zone 3', 'Zone 3', 'Zone 3', 'NONE', 'Zone 3', 'Zone 3', 'Zone 3'),
'SC'=>array(30000, 'NONE', 'Zone 4', 'NONE', 'Zone 4', 'NONE', 'Zone 4', 'NONE', 'Zone 4', 'NONE', 'Zone 4', 'NONE', 'Zone 4'),
'SL'=>array(30000, 'Zone 3', 'Zone 4', 'NONE', 'Zone 4', 'Zone 3', 'Zone 4', 'NONE', 'Zone 4', 'NONE', 'Zone 4', 'NONE', 'Zone 4'),
'SG'=>array(30000, 'Zone 1', 'Zone 2', 'Zone 2', 'Zone 2', 'Zone 1', 'Zone 2', 'Zone 2', 'Zone 2', 'Zone 1', 'Zone 2', 'Zone 2', 'Zone 2'),
'SK'=>array(30000, 'Zone 2-2', 'Zone 3', 'Zone 3', 'Zone 3', 'Zone 2-2', 'Zone 3', 'Zone 3', 'Zone 3', 'Zone 2-2', 'NONE', 'NONE', 'NONE'),
'SI'=>array(30000, 'Zone 2-2', 'Zone 3', 'Zone 3', 'Zone 3', 'Zone 2-2', 'Zone 3', 'Zone 3', 'Zone 3', 'Zone 2-2', 'Zone 3', 'Zone 3', 'Zone 3'),
'SB'=>array(20000, 'Zone 2-1', 'Zone 3', 'NONE', 'Zone 3', 'Zone 2-1', 'Zone 3', 'NONE', 'Zone 3', 'NONE', 'NONE', 'NONE', 'NONE'),
'SO'=>array(20000, 'NONE', 'Zone 4', 'NONE', 'Zone 4', 'NONE', 'Zone 4', 'NONE', 'Zone 4', 'NONE', 'NONE', 'NONE', 'NONE'),
'ZA'=>array(20000, 'Zone 3', 'Zone 4', 'Zone 4', 'Zone 4', 'Zone 3', 'NONE', 'NONE', 'NONE', 'NONE', 'NONE', 'NONE', 'NONE'),
'KR'=>array(30000, 'Zone 1', 'Zone 1', 'NONE', 'Zone 1', 'Zone 1', 'Zone 1', 'NONE', 'Zone 1', 'Zone 1', 'NONE', 'NONE', 'NONE'),
'ES'=>array(30000, 'Zone 2-2', 'Zone 3', 'Zone 3', 'Zone 3', 'Zone 2-2', 'Zone 3', 'Zone 3', 'Zone 3', 'NONE', 'Zone 3', 'Zone 3', 'Zone 3'),
'LK'=>array(30000, 'Zone 1', 'Zone 2', 'Zone 2', 'Zone 2', 'Zone 1', 'Zone 2', 'Zone 2', 'Zone 2', 'Zone 1', 'Zone 2', 'Zone 2', 'Zone 2'),
'SD'=>array(30000, 'Zone 3', 'Zone 4', 'NONE', 'Zone 4', 'Zone 3', 'Zone 4', 'NONE', 'Zone 4', 'Zone 3', 'Zone 4', 'NONE', 'Zone 4'),
'SR'=>array(20000, 'NONE', 'Zone 4', 'NONE', 'Zone 4', 'NONE', 'Zone 4', 'NONE', 'Zone 4', 'NONE', 'NONE', 'NONE', 'NONE'),
'SZ'=>array(20000, 'NONE', 'Zone 4', 'NONE', 'Zone 4', 'NONE', 'Zone 4', 'NONE', 'Zone 4', 'NONE', 'NONE', 'NONE', 'NONE'),
'SE'=>array(30000, 'Zone 2-2', 'Zone 3', 'Zone 3', 'Zone 3', 'Zone 2-2', 'Zone 3', 'Zone 3', 'Zone 3', 'Zone 2-2', 'NONE', 'NONE', 'NONE'),
'CH'=>array(30000, 'Zone 2-2', 'Zone 3', 'Zone 3', 'Zone 3', 'Zone 2-2', 'Zone 3', 'Zone 3', 'Zone 3', 'Zone 2-2', 'Zone 3', 'Zone 3', 'Zone 3'),
'SY'=>array(30000, 'Zone 2-1', 'Zone 3', 'NONE', 'Zone 3', 'Zone 2-1', 'Zone 3', 'NONE', 'Zone 3', 'NONE', 'Zone 3', 'NONE', 'Zone 3'),
'ST'=>array(30000, 'NONE', 'Zone 4', 'NONE', 'Zone 4', 'NONE', 'Zone 4', 'NONE', 'Zone 4', 'NONE', 'Zone 4', 'NONE', 'Zone 4'),
'TW'=>array(30000, 'Zone 1', 'Zone 1', 'NONE', 'Zone 1', 'Zone 1', 'Zone 1', 'NONE', 'Zone 1', 'Zone 1', 'Zone 1', 'NONE', 'Zone 1'),
'TJ'=>array(30000, 'NONE', 'Zone 3', 'Zone 3', 'Zone 3', 'NONE', 'Zone 3', 'Zone 3', 'Zone 3', 'NONE', 'NONE', 'NONE', 'Zone 3'),
'TZ'=>array(30000, 'Zone 3', 'Zone 4', 'NONE', 'Zone 4', 'Zone 3', 'Zone 4', 'NONE', 'Zone 4', 'Zone 3', 'Zone 4', 'NONE', 'Zone 4'),
'TH'=>array(30000, 'Zone 1', 'Zone 2', 'Zone 2', 'Zone 2', 'Zone 1', 'Zone 2', 'Zone 2', 'Zone 2', 'Zone 1', 'Zone 2', 'Zone 2', 'Zone 2'),
'TL'=>array(10000, 'NONE', 'Zone 2', 'NONE', 'Zone 2', 'NONE', 'NONE', 'NONE', 'NONE', 'NONE', 'NONE', 'NONE', 'NONE'),
'TG'=>array(30000, 'Zone 3', 'Zone 4', 'NONE', 'Zone 4', 'Zone 3', 'Zone 4', 'NONE', 'Zone 4', 'Zone 3', 'Zone 4', 'NONE', 'Zone 4'),
'TO'=>array(20000, 'NONE', 'Zone 3', 'NONE', 'Zone 3', 'NONE', 'Zone 3', 'NONE', 'Zone 3', 'NONE', 'NONE', 'NONE', 'NONE'),
'TT'=>array(20000, 'Zone 2-1', 'Zone 3', 'NONE', 'Zone 3', 'Zone 2-1', 'NONE', 'NONE', 'NONE', 'NONE', 'NONE', 'NONE', 'NONE'),
'TN'=>array(30000, 'Zone 3', 'Zone 4', 'NONE', 'Zone 4', 'Zone 3', 'Zone 4', 'NONE', 'Zone 4', 'NONE', 'Zone 4', 'NONE', 'Zone 4'),
'TR'=>array(30000, 'Zone 2-1', 'Zone 3', 'Zone 3', 'Zone 3', 'Zone 2-1', 'Zone 3', 'Zone 3', 'Zone 3', 'Zone 2-1', 'Zone 3', 'Zone 3', 'Zone 3'),
'TM'=>array(20000, 'NONE', 'Zone 3', 'Zone 3', 'Zone 3', 'NONE', 'Zone 3', 'Zone 3', 'Zone 3', 'NONE', 'NONE', 'NONE', 'NONE'),
'TC'=>array(10000, 'NONE', 'Zone 3', 'NONE', 'Zone 3', 'NONE', 'NONE', 'NONE', 'NONE', 'NONE', 'NONE', 'NONE', 'NONE'),
'TV'=>array(20000, 'NONE', 'Zone 3', 'NONE', 'Zone 3', 'NONE', 'Zone 3', 'NONE', 'Zone 3', 'NONE', 'NONE', 'NONE', 'NONE'),
'VI'=>array(30000, 'NONE', 'Zone 3', 'Zone 3', 'Zone 3', 'NONE', 'Zone 3', 'Zone 3', 'Zone 3', 'NONE', 'Zone 3', 'Zone 3', 'Zone 3'),
'UG'=>array(30000, 'Zone 3', 'Zone 4', 'NONE', 'Zone 4', 'Zone 3', 'Zone 4', 'NONE', 'Zone 4', 'Zone 3', 'Zone 4', 'NONE', 'Zone 4'),
'UA'=>array(30000, 'Zone 2-2', 'Zone 3', 'Zone 3', 'Zone 3', 'Zone 2-2', 'Zone 3', 'Zone 3', 'Zone 3', 'NONE', 'Zone 3', 'Zone 3', 'Zone 3'),
'AE'=>array(30000, 'Zone 2-1', 'Zone 3', 'Zone 3', 'Zone 3', 'Zone 2-1', 'Zone 3', 'Zone 3', 'Zone 3', 'Zone 2-1', 'Zone 3', 'Zone 3', 'Zone 3'),
'GB'=>array(30000, 'Zone 2-2', 'Zone 3', 'Zone 3', 'Zone 3', 'Zone 2-2', 'Zone 3', 'Zone 3', 'Zone 3', 'Zone 2-2', 'Zone 3', 'Zone 3', 'Zone 3'),
'US'=>array(30000, 'Zone 2-1', 'Zone 3', 'Zone 3', 'Zone 3', 'Zone 2-1', 'Zone 3', 'Zone 3', 'Zone 3', 'Zone 2-1', 'Zone 3', 'Zone 3', 'Zone 3'),
'UY'=>array(30000, 'Zone 3', 'Zone 4', 'NONE', 'Zone 4', 'Zone 3', 'Zone 4', 'NONE', 'Zone 4', 'Zone 3', 'Zone 4', 'NONE', 'Zone 4'),
'UZ'=>array(30000, 'NONE', 'Zone 3', 'Zone 3', 'Zone 3', 'NONE', 'Zone 3', 'Zone 3', 'Zone 3', 'NONE', 'NONE', 'NONE', 'Zone 3'),
'VU'=>array(20000, 'NONE', 'Zone 3', 'NONE', 'Zone 3', 'NONE', 'Zone 3', 'NONE', 'Zone 3', 'NONE', 'NONE', 'NONE', 'NONE'),
'VA'=>array(20000, 'NONE', 'Zone 3', 'Zone 3', 'Zone 3', 'NONE', 'Zone 3', 'Zone 3', 'Zone 3', 'NONE', 'NONE', 'NONE', 'NONE'),
'VE'=>array(30000, 'Zone 3', 'Zone 4', 'NONE', 'Zone 4', 'Zone 3', 'Zone 4', 'NONE', 'Zone 4', 'Zone 3', 'Zone 4', 'NONE', 'Zone 4'),
'VN'=>array(30000, 'Zone 1', 'Zone 2', 'NONE', 'Zone 2', 'Zone 1', 'Zone 2', 'NONE', 'Zone 2', 'Zone 1', 'Zone 2', 'NONE', 'Zone 2'),
'WF'=>array(20000, 'NONE', 'Zone 3', 'NONE', 'Zone 3', 'NONE', 'Zone 3', 'NONE', 'Zone 3', 'NONE', 'NONE', 'NONE', 'NONE'),
'YE'=>array(30000, 'NONE', 'Zone 3', 'NONE', 'Zone 3', 'NONE', 'Zone 3', 'NONE', 'Zone 3', 'NONE', 'Zone 3', 'NONE', 'Zone 3'),
'ZM'=>array(30000, 'NONE', 'Zone 4', 'NONE', 'Zone 4', 'NONE', 'Zone 4', 'NONE', 'Zone 4', 'NONE', 'Zone 4', 'NONE', 'Zone 4'),
'ZW'=>array(30000, 'Zone 3', 'Zone 4', 'NONE', 'Zone 4', 'Zone 3', 'Zone 4', 'NONE', 'Zone 4', 'NONE', 'Zone 4', 'NONE', 'Zone 4')	
	);

		// Return null if the requested countryInfo do not exist
		if(!array_key_exists($dstCountryId, $countryInfo)) {
			return FALSE;
		}
		// Return the requested countryInfo
		return $countryInfo[$dstCountryId];
	}
}