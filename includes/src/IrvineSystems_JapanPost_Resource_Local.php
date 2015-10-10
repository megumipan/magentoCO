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
 
class IrvineSystems_JapanPost_Resource_Local
{
	/**
	 * External Price list php File Name
	 * This will allow customer to edit their own Price list
	 * Maintaining internal Rules
	 */
	private $priceList = 'japanpost/localpricelist';

    /**
     * Distan Matrix
     */
    protected $_distanceMatrix = array(
        'AA'=>'N0',
        'AB'=>'N2','BB'=>'N1',
        'AC'=>'N4','BC'=>'N1','CC'=>'N1',
        'AD'=>'N4','BD'=>'N1','CD'=>'N1','DD'=>'N1',
        'AE'=>'N5','BE'=>'N2','CE'=>'N1','DE'=>'N1','EE'=>'N1',
        'AF'=>'N5','BF'=>'N2','CF'=>'N1','DF'=>'N1','EF'=>'N1','FF'=>'N1',
        'AG'=>'N6','BG'=>'N3','CG'=>'N2','DG'=>'N2','EG'=>'N1','FG'=>'N1','GG'=>'N1',
        'AH'=>'N7','BH'=>'N4','CH'=>'N3','DH'=>'N3','EH'=>'N2','FH'=>'N2','GH'=>'N1','HH'=>'N1',
        'AI'=>'N7','BI'=>'N4','CI'=>'N3','DI'=>'N3','EI'=>'N2','FI'=>'N2','GI'=>'N1','HI'=>'N1','II'=>'N1',
        'AJ'=>'N7','BJ'=>'N6','CJ'=>'N5','DJ'=>'N5','EJ'=>'N3','FJ'=>'N3','GJ'=>'N2','HJ'=>'N1','IJ'=>'N2','JJ'=>'N1',
        'AK'=>'N7','BK'=>'N7','CK'=>'N6','DK'=>'N7','EK'=>'N7','FK'=>'N6','GK'=>'N6','HK'=>'N5','IK'=>'N6','JK'=>'N3','KK'=>'N0',
    );
	
	/**
	* Get the Cooling shipment Extra rate according to cart dimension and weight
	*
	* @return float||int
	*/
    public function _getCoolRate()
    {
		// Get Cart Weight
		$cartWeight = Mage::getSingleton('japanpost/session')->getPackageWeight();
		// Get Cart Dimensions
		$cartSize = Mage::getSingleton('japanpost/session')->getPackageSize();
		$cartSize = $cartSize['Vol_Basic'];
    	//Get the Cool Shipments Rates from the open file
		$_coolCharges = Mage::getResourceModel($this->priceList)->__get('_coolCharges');

		// Then we select the Index for the local Shipping Size Class
		switch(true){
		    case ($cartSize <=  600):	return $_coolCharges[0];
		    case ($cartSize <=  800):	return $_coolCharges[1];
		    case ($cartSize <= 1000):	return $_coolCharges[2];
		    case ($cartSize <= 1200):	return $_coolCharges[3];
		    case ($cartSize <= 1400):	return $_coolCharges[4];
		    case ($cartSize <= 1500):	return $_coolCharges[5];
		}
		// If no prices are available return null
		return null;
	}

	/**
	* Get Teikei Price Class
	*
	* @param $weight = Cart weight
	* @return int
	*/
    protected function _getTeikeiClass($weight)
    {
		switch(true){
		    case ($weight <= 25):	return 0;
		    case ($weight <= 50):	return 1;
		}
		return null;
	}

	/**
	* Get TeikeiGai Price Class
	*
	* @param $weight = Cart weight
	* @return int
	*/
    protected function _getTeikeiGaiClass($weight)
    {
		switch(true){
		    case ($weight <= 50):	return 0;
		    case ($weight <= 100):	return 1;
		    case ($weight <= 150):	return 2;
		    case ($weight <= 250):	return 3;
		    case ($weight <= 500):	return 4;
		    case ($weight <= 1000):	return 5;
		    case ($weight <= 2000): return 6;
		    case ($weight <= 4000): return 7;
		}
		return null;
	}

	/**
	* Get The Size Class for the given Order dimensions
	*
	* @param $orderDimension = Cart Dimensions
	* @param $maxDimension = Dimensions Limit
	* @return int
	*/
    protected function _getYuuPackClass($orderDimension)
    {
		// Maximum allow Dimensions
		$maxDimension = Mage::getStoreConfig('japanpost/yuupack/maxsize');
		
		// Then we select the Index for the local Shipping Size Class
		switch(true){
		    case ($orderDimension <= 600):				return 0;
		    case ($orderDimension <= 800):				return 1;
		    case ($orderDimension <= 1000):				return 2;
		    case ($orderDimension <= 1200):				return 3;
		    case ($orderDimension <= 1400):				return 4;
		    case ($orderDimension <= 1600):				return 5;
		    case ($orderDimension <= $maxDimension):	return 6;
		}
		return null;
	}

	/**
	* Validate if Teikei Method is eligible to be processed
	*
	* @param int $orderDimension = Order Base Volume Dimensions
	* @return bool
	*/
    public function validateYuuPack($orderDimension)
    {
		// Maximum allow Dimensions
		$maxDimension = Mage::getStoreConfig('japanpost/yuupack/maxsize');

		// Check Maximum Dimensions
		if($orderDimension > $maxDimension) return false;

		// If non of the above where true Teikei is valid
		return true;
	}

	/**
	* Validate if Cooling Shipment Methods are eligible to be processed
	*
	* @param int $orderDimension	= Order Base Volume Dimensions
	* @param int $cartWeight 		= Order Total Weight
	* @return bool
	*/
    public function validateCool($cartSize,$cartWeight)
    {
		// Compare Dimensions
		if($cartSize['Vol_Length']	> 1000)		return false;
		if($cartSize['Vol_Basic']	> 1500)		return false;
		if($cartWeight				> 30000)	return false;
		// If valid return true
		return true;
	}

	/**
	* Validate if Teikei Method is eligible to be processed
	*
	* @param int|float $weight = Cart Weight
	* @param array $rawDimension = Raw cart dimension (L,W,H)
	* @return bool
	*/
    public function validateTeikei($weight,$rawDimension)
    {
		// Get Method Limitations
		$minLenght	= Mage::getStoreConfig('japanpost/teikei/minlenght');
		$minWidth	= Mage::getStoreConfig('japanpost/teikei/minwidth');
		$maxWeight	= Mage::getStoreConfig('japanpost/teikei/maxweight');
		$maxLenght	= Mage::getStoreConfig('japanpost/teikei/maxlenght');
		$maxWidth	= Mage::getStoreConfig('japanpost/teikei/maxwidth');
		$maxDepth	= Mage::getStoreConfig('japanpost/teikei/maxdepth');

		// Check Maximum Weight
		if ($weight>$maxWeight) return false;

		// Check Minimum Dimensions
		if ($rawDimension[1]<$minWidth || $rawDimension[2]<$minLenght) return false;

		// Check Maximum Dimensions
		if ($rawDimension[0]>$maxDepth || $rawDimension[1]>$maxWidth || $rawDimension[2]>$maxLenght) return false;
		
		// If non of the above where true Teikei is valid
		return true;
	}

	/**
	* Validate if TeikeiGai Method is eligible to be processed
	*
	* @param int|float $weight = Cart Weight
	* @param array $rawDimension = Raw cart dimension (L,W,H)
	* @return bool
	*/
    public function validateTeikeiGai($weight,$rawDimension)
    {
		// Get Method Limitations
		$minLenght	= Mage::getStoreConfig('japanpost/teikeigai/minlenght');
		$minWidth	= Mage::getStoreConfig('japanpost/teikeigai/minwidth');
		$maxWeight	= Mage::getStoreConfig('japanpost/teikeigai/maxweight');
		$maxLenght	= Mage::getStoreConfig('japanpost/teikeigai/maxlenght');
		$maxBaseVol	= Mage::getStoreConfig('japanpost/teikeigai/maxsize');

		// Check Maximum Weight
		if ($weight>$maxWeight) return false;

		// Check Minimum Dimensions
		if ($rawDimension[1]<$minWidth || $rawDimension[2]<$minLenght) return false;

		// Check Maximum Dimensions
		$cartBaseVol = $rawDimension[0]+$rawDimension[1]+$rawDimension[2];
		if ($cartBaseVol>$maxBaseVol || $rawDimension[2]>$maxLenght) return false;
		
		// If non of the above where true Teikei is valid
		return true;
	}

	/**
	* Get The rate for the shipping
	*
	* @param $data = Rate Calculation Data
	*
	* @return int
	*/
    public function getRate($data)
    {
		// Get the method Specific Rate
		switch($data['method_code']){
		    case ('yuupack'):
				$rate = $this->_yuupackRate($data);
		    break;
		    case ('teikei'):
				$rate = $this->_teikeiRate($data['weight']);
		    break;
		    case ('teikeigai'):
				$rate = $this->_teikeiGaiRate($data['weight']);
		    break;
		}

		// Return the Rate
        return $rate;
    }

	/**
	* Generate the YuuPack Rate
	*
	* @param $data = Rate Calculation Data
	*
	* @return int
	*/
    protected function _yuupackRate($data)
    {
    	//Get the Teikei Rates from the open file
		$_yuuPackRates = Mage::getResourceModel($this->priceList)->__get('_yuuPackRates');

		// Get the Zone Code for Source and Destination Prefectures
        $_srcProv = $this->_getZoneCode($data['srcProvince']);
        $_dstProv = $this->_getZoneCode($data['dstProvince']);

		if($data['srcProvince']==$data['dstProvince']){
			// If source and destination prefecture are the same the matrix value will be the first
    	    $_mtxVal = 'N0';
		}else{
			// Get the Matrix key for the given prefectures
			$_mtxKey = (ord($_srcProv) < ord($_dstProv)) ? ($_srcProv . $_dstProv) : ($_dstProv . $_srcProv);

			// Get the Matrix Value for the required Distance
    	    $_mtxVal = $this->_distanceMatrix[$_mtxKey];
		}

		// Get the Shipping Class
        $class = $this->_getYuuPackClass($data['orderDimension']);

		// Get the rate for the given distance and Size class
        $rate = $_yuuPackRates[$_mtxVal][$class];

		// Return the Rate
        return $rate;
    }

	/**
	* Generate the Teikei Rate
	*
	* @param $data = Rate Calculation Data
	*
	* @return int
	*/
    protected function _teikeiRate($weight)
    {
    	//Get the Teikei Rates from the open file
		$_teikeiRates = Mage::getResourceModel($this->priceList)->__get('_teikeiRates');

		// Get the Shipping Class
		$class = $this->_getTeikeiClass($weight);
		
		// Set the Rate
		$rate = $_teikeiRates[$class];

		// Return the Rate
        return $rate;
    }

	/**
	* Generate the TeikeiGai Rate
	*
	* @param $data = Rate Calculation Data
	*
	* @return int
	*/
    protected function _teikeiGaiRate($weight)
    {
    	//Get the Teikei Rates from the open file
		$_teikeiGaiRates = Mage::getResourceModel($this->priceList)->__get('_teikeiGaiRates');

		// Get the Shipping Class
		$class = $this->_getTeikeiGaiClass($weight);
		
		// Set the Rate
		$rate = $_teikeiGaiRates[$class];

		// Return the Rate
        return $rate;
    }

	/**
	* Get the Zone Code according to the Given Prefecture
	*
	* @param $key = Japanese name of the prefecture
	* @return sting
	*/
    public function _getZoneCode($key) {
        $_zoneMap = array(
            '北海道'    => 'A',
            '青森県'    => 'B',
            '岩手県'    => 'B',
            '秋田県'    => 'B',
            '宮城県'    => 'B',
            '山形県'    => 'B',
            '福島県'    => 'B',
            '茨城県'    => 'C',
            '栃木県'    => 'C',
            '群馬県'    => 'C',
            '埼玉県'    => 'C',
            '千葉県'    => 'C',
            '東京都'    => 'C',
            '神奈川県'  => 'C',
            '山梨県'    => 'C',
            '新潟県'    => 'D',
            '長野県'    => 'D',
            '富山県'    => 'E',
            '石川県'    => 'E',
            '福井県'    => 'E',
            '岐阜県'    => 'F',
            '静岡県'    => 'F',
            '愛知県'    => 'F',
            '三重県'    => 'F',
            '滋賀県'    => 'G',
            '京都府'    => 'G',
            '大阪府'    => 'G',
            '兵庫県'    => 'G',
            '奈良県'    => 'G',
            '和歌山県'  => 'G',
            '鳥取県'    => 'H',
            '島根県'    => 'H',
            '岡山県'    => 'H',
            '広島県'    => 'H',
            '山口県'    => 'H',
            '徳島県'    => 'I',
            '香川県'    => 'I',
            '愛媛県'    => 'I',
            '高知県'    => 'I',
            '福岡県'    => 'J',
            '佐賀県'    => 'J',
            '長崎県'    => 'J',
            '熊本県'    => 'J',
            '大分県'    => 'J',
            '宮崎県'    => 'J',
            '鹿児島県'  => 'J',
            '沖縄県'    => 'K'
        );
		// If the value is available return it
        return (isset($_zoneMap[$key])) ? $_zoneMap[$key] : FALSE;
    }
}