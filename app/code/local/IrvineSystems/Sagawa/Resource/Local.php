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
 
class IrvineSystems_Sagawa_Resource_Local
{
	/**
	 * External Price list php File Name
	 * This will allow customer to edit their own Price list
	 * Maintaining internal Rules
	 */
	private $priceList = 'sagawa/localpricelist';

	/**
     * Distan Matrix
     */
    protected $_distanceMatrix = array(
        'AA'=>'N0',
        'AB'=>'N2', 'BB'=>'N0',
        'AC'=>'N3', 'BC'=>'N0','CC'=>'N0',
        'AD'=>'N4', 'BD'=>'N1','CD'=>'N0','DD'=>'N0',
        'AE'=>'N4', 'BE'=>'N1','CE'=>'N0','DE'=>'N0','EE'=>'N0',
        'AF'=>'N5', 'BF'=>'N2','CF'=>'N1','DF'=>'N0','EF'=>'N0','FF'=>'N0',
        'AG'=>'N5', 'BG'=>'N2','CG'=>'N1','DG'=>'N0','EG'=>'N0','FG'=>'N0','GG'=>'N0',
        'AH'=>'N7', 'BH'=>'N3','CH'=>'N2','DH'=>'N1','EH'=>'N1','FH'=>'N0','GH'=>'N0','HH'=>'N0',
        'AI'=>'N8', 'BI'=>'N4','CI'=>'N4','DI'=>'N2','EI'=>'N2','FI'=>'N1','GI'=>'N1','HI'=>'N0','II'=>'N0',
        'AJ'=>'N9', 'BJ'=>'N5','CJ'=>'N5','DJ'=>'N3','EJ'=>'N3','FJ'=>'N2','GJ'=>'N2','HJ'=>'N1','IJ'=>'N1','JJ'=>'N0',
        'AK'=>'N10','BK'=>'N6','CK'=>'N6','DK'=>'N4','EK'=>'N4','FK'=>'N2','GK'=>'N2','HK'=>'N1','IK'=>'N0','JK'=>'N1','KK'=>'N0',
        'AL'=>'N10','BL'=>'N6','CL'=>'N6','DL'=>'N4','EL'=>'N4','FL'=>'N2','GL'=>'N2','HL'=>'N1','IL'=>'N0','JL'=>'N1','KL'=>'N0','LL'=>'N0',
    );

	/**
	* Get The Size Class for the given Order dimensions
	*
	* @param $orderDimension = Cart Dimensions
	* @param $maxDimension = Dimensions Limit
	* @return int
	*/
    public function _getSizeClass($orderDimension,$cartWeight)
    {
		// Then we select the Index for the local Shipping Size Class
		switch(true){
		    case ($orderDimension <= 600 && $cartWeight <= 2000):
				return 0;
		    case ($orderDimension <= 800 && $cartWeight <= 5000):
				return 1;
		    case ($orderDimension <= 1000 && $cartWeight <= 10000):
				return 2;
		    case ($orderDimension <= 1400 && $cartWeight <= 20000):
				return 3;
		    case ($orderDimension <= 1600 && $cartWeight <= 30000):
				return 4;
		    case ($orderDimension <= 1700):
				return 5;
		    case ($orderDimension <= 1800):
				return 6;
		    case ($orderDimension <= 2000):
				return 7;
		    case ($orderDimension <= 2200):
				return 8;
		    case ($orderDimension <= 2400):
				return 9;
		    case ($orderDimension <= 2600):
				return 10;
		}
		return null;
	}

    /**
     * Get Cash on delivery extra charge cost
     *
     * @param $cartPrice = Shopping Cart value (subtotal)
     *
     * @return int - cash on delivery extra cost
     */
    public function getCodCharge($cartPrice)
    {
    	//Get the Cash on Devlivery Charges from the open file
		$_codCharges = Mage::getResourceModel($this->priceList)->__get('_codCharges');

		// Get the Cash on Delivery charge from Config
    	switch(true){
	    	case $cartPrice <= 10000:	return $_codCharges[0];
	    	case $cartPrice <= 30000:	return $_codCharges[1];
	    	case $cartPrice <= 100000:	return $_codCharges[2];
	    	case $cartPrice <= 300000:	return $_codCharges[3];
	    	case $cartPrice <= 500000:	return $_codCharges[4];
	    	case $cartPrice <= 1000000:	return $_codCharges[5];
	    	default:					return $_codCharges[6];
    	}
		// If no prices are available return null
		return null;
    }

	/**
	* Get the Cooling shipment Extra rate according to cart dimension and weight
	*
	* @return float||int
	*/
    public function getCoolRate($cartSize,$cartWeight)
    {
		// Then we select the Index for the local Shipping Size Class
    	$_coolCharges = Mage::getResourceModel($this->priceList)->__get('_coolCharges');
    	switch(true){
		    case ($cartSize <= 800 && $cartWeight <= 5000):	return $_coolCharges[0];
		    case ($cartSize <= 800 && $cartWeight <= 5000):	return $_coolCharges[1];
		    case ($cartSize <= 1000 && $cartWeight <= 10000):return $_coolCharges[2];
		    case ($cartSize <= 1400 && $cartWeight <= 20000):return $_coolCharges[3];
		    case ($cartSize <= 1400 && $cartWeight <= 30000):return $_coolCharges[4];
		    case ($cartSize <= 1400 && $cartWeight <= 40000):return $_coolCharges[5];
		    case ($cartSize <= 1400 && $cartWeight <= 50000):return $_coolCharges[6];
		}
		// If no prices are available return null
		return null;
	}

	/**
	* Validate if Mail Shipment Methods are eligible to be processed
	*
	* @param int $cartSize	= Order Dimensions
	* @param int $cartWeight 		= Order Total Weight
	* @return bool
	*/
    public function validateMail($method_code,$cartSize,$cartWeight)
    {
		// Compare Dimensions
    	// max 400
    	if($cartSize['Raw_Sizes'][2] > Mage::getStoreConfig('sagawa/'.$method_code.'/maxlength'))	return false;
    	// depth(min) 20
    	if($cartSize['Raw_Sizes'][0] > Mage::getStoreConfig('sagawa/'.$method_code.'/maxdepth'))	return false;
    	// total 700
		if($cartSize['Vol_Basic']    > Mage::getStoreConfig('sagawa/'.$method_code.'/maxtotal'))	return false;
		// weight 1000
		if($cartWeight > Mage::getStoreConfig('sagawa/'.$method_code.'/maxweight'))	return false;
		// If valid return true
		return true;
	}

	/**
	* Validate if Cooling Shipment Methods are eligible to be processed
	*
	* @param int $cartSize	= Order Base Volume Dimensions
	* @param int $cartWeight = Order Total Weight
	* @return bool
	*/
    public function validateCool($cartSize,$cartWeight)
    {
		// Compare Dimensions
		if($cartSize	> 1400)		return false;
		if($cartWeight	> 50000)	return false;
		// If valid return true
		return true;
	}
	
	/**
	* Get The rate for the shipping
	*
	* @param $data = Rate Calculation Data
	* @return int
	*/
    public function getRate($data)
    {
		$srcProvince = $data['srcProvince'];
		$dstProvince = $data['dstProvince'];
		$sizeClass = $data['sizeClass'];
		$cartWeight = $data['weight'];
		
		$_freeSizeShippingAddPrice = Mage::getResourceModel($this->priceList)->__get('_freeSizeShippingAddPrice');
		
		$expressMaxSize = $_freeSizeShippingAddPrice[0];
		$countSize = $_freeSizeShippingAddPrice[1];
		$addPriceByWeight = $_freeSizeShippingAddPrice[2];

    	// Get the Zone Code for Source and Destination Prefectures
        $_srcProv = $this->_getZoneCode($srcProvince);
        $_dstProv = $this->_getZoneCode($dstProvince);

		// Get the Matrix key for the given prefectures
		$_mtxKey = (ord($_srcProv) < ord($_dstProv)) ? ($_srcProv . $_dstProv) : ($_dstProv . $_srcProv);

		// Get the Matrix Value for the required Distance
        $_mtxVal = $this->_distanceMatrix[$_mtxKey];

		// Get the rate for the given distance and Size class
        $_basicRates = Mage::getResourceModel($this->priceList)->__get('_basicRates');
        $rate = $_basicRates[$_mtxVal][$sizeClass];
   		if($cartWeight > $expressMaxSize){
			switch(true){
				// Now, cartWeight can't over 50kg. we must be checking 40kg and 30kg only
				case $cartWeight > $expressMaxSize+$countSize:
					$rate=$rate+$addPriceByWeight;
				case $cartWeight > $expressMaxSize:
					$rate=$rate+$addPriceByWeight;
			}
		}

        // Return the Rate
        return $rate;
    }

    public function getMailRate($cartWeight){
    	$_mailCharges = Mage::getResourceModel($this->priceList)->__get('_mailCharges');
    	switch(true){
    	case $cartWeight <= 300:
	    	return $_mailCharges[0];
    	case $cartWeight <= 600:
	    	return $_mailCharges[1];
    	default:
	    	return $_mailCharges[2];
    	}
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
            '宮城県'    => 'C',
            '山形県'    => 'C',
            '福島県'    => 'C',
            '茨城県'    => 'D',
            '栃木県'    => 'D',
            '群馬県'    => 'D',
            '埼玉県'    => 'D',
            '千葉県'    => 'D',
            '東京都'    => 'D',
            '神奈川県'  => 'D',
            '山梨県'    => 'D',
            '新潟県'    => 'E',
            '長野県'    => 'E',
            '富山県'    => 'G',
            '石川県'    => 'G',
            '福井県'    => 'G',
            '岐阜県'    => 'F',
            '静岡県'    => 'F',
            '愛知県'    => 'F',
            '三重県'    => 'F',
            '滋賀県'    => 'H',
            '京都府'    => 'H',
            '大阪府'    => 'H',
            '兵庫県'    => 'H',
            '奈良県'    => 'H',
            '和歌山県'  => 'H',
            '鳥取県'    => 'I',
            '島根県'    => 'I',
            '岡山県'    => 'I',
            '広島県'    => 'I',
            '山口県'    => 'I',
            '徳島県'    => 'J',
            '香川県'    => 'J',
            '愛媛県'    => 'J',
            '高知県'    => 'J',
            '福岡県'    => 'K',
            '佐賀県'    => 'K',
            '長崎県'    => 'K',
            '熊本県'    => 'L',
            '大分県'    => 'K',
            '宮崎県'    => 'L',
            '鹿児島県'  => 'L'#,
            #'沖縄県'    => 'K'
        );
		// If the value is available return it
        return (isset($_zoneMap[$key])) ? $_zoneMap[$key] : FALSE;
    }
}