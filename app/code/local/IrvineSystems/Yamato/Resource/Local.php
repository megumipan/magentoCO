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
 
class IrvineSystems_Yamato_Resource_Local
{
	/**
	 * External Price list php File Name
	 * This will allow customer to edit their own Price list
	 * Maintaining internal Rules
	 */
	private $priceList = 'yamato/localpricelist';

    /**
     * Distan Matrix
     */
    protected $_distanceMatrix = array(
        'AA'=>'N0',
        'AB'=>'N2', 'BB'=>'N0',
        'AC'=>'N3', 'BC'=>'N0', 'CC'=>'N0',
        'AD'=>'N4', 'BD'=>'N1', 'CD'=>'N0', 'DD'=>'N0',
        'AE'=>'N4', 'BE'=>'N1', 'CE'=>'N0', 'DE'=>'N0', 'EE'=>'N0',
        'AF'=>'N5', 'BF'=>'N2', 'CF'=>'N1', 'DF'=>'N0', 'EF'=>'N0', 'FF'=>'N0',
        'AG'=>'N5', 'BG'=>'N2', 'CG'=>'N1', 'DG'=>'N0', 'EG'=>'N0', 'FG'=>'N0', 'GG'=>'N0',
        'AH'=>'N7', 'BH'=>'N3', 'CH'=>'N2', 'DH'=>'N1', 'EH'=>'N1', 'FH'=>'N0', 'GH'=>'N0', 'HH'=>'N0',
        'AI'=>'N8', 'BI'=>'N4', 'CI'=>'N4', 'DI'=>'N2', 'EI'=>'N2', 'FI'=>'N1', 'GI'=>'N1', 'HI'=>'N0', 'II'=>'N0',
        'AJ'=>'N9', 'BJ'=>'N5', 'CJ'=>'N5', 'DJ'=>'N3', 'EJ'=>'N3', 'FJ'=>'N2', 'GJ'=>'N2', 'HJ'=>'N1', 'IJ'=>'N1', 'JJ'=>'N0',
        'AK'=>'N10','BK'=>'N6', 'CK'=>'N6', 'DK'=>'N4', 'EK'=>'N4', 'FK'=>'N2', 'GK'=>'N2', 'HK'=>'N1', 'IK'=>'N0', 'JK'=>'N1', 'KK'=>'N0',
        'AL'=>'N16','BL'=>'N15','CL'=>'N14','DL'=>'N12','EL'=>'N13','FL'=>'N12','GL'=>'N13','HL'=>'N12','IL'=>'N12','JL'=>'N12','KL'=>'N11','LL'=>'N0',
    );

    /**
     * Get Mail Price Class
     *
     * @param $height = Cart height
     * @return int
     */
    protected function _getMailBinClass($height)
    {
    	switch(true){
    		case ($height <= 10):	return 0;
    		case ($height <= 20):	return 1;
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
		    case ($orderDimension <= 1200 && $cartWeight <= 15000):
		    	return 3;
		    case ($orderDimension <= 1400 && $cartWeight <= 20000):
		    	return 4;
		    case ($orderDimension <= 1600 && $cartWeight <= 25000):
		    	return 5;
		}
		return null;
	}

    /**
     * Get Cash on delivery extra fee
     *
     * @param $cartPrice = Cart subtotal Value
     *
     * @return int - cash on delivery extra fee
     */
    public function getCodCharge($cartPrice)
    {
    	//Get the Cash on Devlivery Charges from the open file
		$_codCharges = Mage::getResourceModel($this->priceList)->__get('_codCharges');

		// Get the Cash on Delivery charge from Config
    	switch(true){
	    	case $cartPrice < 10000:	return $_codCharges[0]; // Bellow 10000 JPY
	    	case $cartPrice < 30000:	return $_codCharges[1]; // Bellow 30000 JPY
	    	case $cartPrice < 100000:	return $_codCharges[2]; // Bellow 100000 JPY
	    	default:					return $_codCharges[3]; // Bellow or equal to 300000 JPY
    	}
		
		// If no prices are available return null
		return null;
    }
    
    /**
     * Get Cash on delivery extra charge tax
     *
     * @param $cartPrice = Cart subtotal Value
     *
     * @return int - cash on delivery fee tax
     */
    public function getCodChargeTax($cartPrice)
    {
    	//Get the Cash on Devlivery Charges from the open file
		$_codTaxCharges = Mage::getResourceModel($this->priceList)->__get('_codTaxCharges');

		// Get the Cash on Delivery charge from Config
    	switch(true){
	    	case $cartPrice < 10000:	return $_codTaxCharges[0]; // Bellow 10000 JPY
	    	case $cartPrice < 30000:	return $_codTaxCharges[1]; // Bellow 30000 JPY
	    	case $cartPrice < 100000:	return $_codTaxCharges[2]; // Bellow 100000 JPY
	    	default:					return $_codTaxCharges[3]; // Bellow or equal to 300000 JPY
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
    	//Get the Cool Shipment Charges from the open file
		$_coolCharges = Mage::getResourceModel($this->priceList)->__get('_coolCharges');

		// Then we select the Index for the local Shipping Size Class
		switch(true){
		    case ($cartSize <=  600	&& $cartWeight <=  2000): return $_coolCharges[0];
		    case ($cartSize <=  800	&& $cartWeight <=  5000): return $_coolCharges[1];
		    case ($cartSize <= 1000	&& $cartWeight <= 10000): return $_coolCharges[2];
		    case ($cartSize <= 1200	&& $cartWeight <= 15000): return $_coolCharges[3];
		}
		// If no prices are available return null
		return null;
	}

    /**
	 * Validate if Cash on delivery service are eligible to be processed
     *
     * @param $cartPrice = Cart subtotal Value
     *
	* @return bool
     */
    public function validateCod($cartPrice)
    {
		// If the cart value is above the maximum amount return false
		if($cartPrice > 300000) return false;
		// Otherwise return true
		return true;
	}
	
	/**
	* Validate if Mail-BIN Methods are eligible to be processed
	*
	* @param int $cartSize		= Order Base Volume Size
	* @param int $cartWeight 	= Order Total Weight
	* @return bool
	*/
    public function validateMailBin($cartSize,$cartWeight)
    {
		$method_code = 'mailbin';
		// Compare Dimensions
    	if($cartSize['Raw_Sizes'][0] > Mage::getStoreConfig('yamato/'.$method_code.'/maxdepth'))	return false;
		if($cartSize['Raw_Sizes'][1] > Mage::getStoreConfig('yamato/'.$method_code.'/maxwidth'))	return false;
		if($cartSize['Raw_Sizes'][2] > Mage::getStoreConfig('yamato/'.$method_code.'/maxlenght'))	return false;
		if($cartSize['Vol_Basic']    > Mage::getStoreConfig('yamato/'.$method_code.'/maxtotal'))	return false;
		if($cartWeight > Mage::getStoreConfig('yamato/'.$method_code.'/maxweight'))	return false;
		// If valid return true
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
		if($cartSize	> 1200)		return false;
		if($cartWeight	> 15000)	return false;
		// If valid return true
		return true;
	}

	/**
	* Get The rate for the shipping
	*
	* @param $height = Greatest measure of the shopping cart
	*
	* @return int
	*/
    public function getMailRate($height)
    {
    	//Get the Mail-bin shipping rate from the open file
		$_mailCharges = Mage::getResourceModel($this->priceList)->__get('_mailCharges');

   		// Get the Shipping Class
   		$class = $this->_getMailBinClass($height);
   		// Set the Rate
   		$rate = $_mailCharges[$class];
		// Return the Rate
        return $rate;
    }

	/**
	* Get The rate for the shipping
	*
	* @param $data = Rate Calculation Data
	* @return int
	*/
    public function getRate($data)
    {
		// Get the Zone Code for Source and Destination Prefectures
        $_srcProv = $this->_getZoneCode($data['srcProvince']);
        $_dstProv = $this->_getZoneCode($data['dstProvince']);

		// Get the Matrix key for the given prefectures
		$_mtxKey = (ord($_srcProv) < ord($_dstProv)) ? ($_srcProv . $_dstProv) : ($_dstProv . $_srcProv);

		// Get the Matrix Value for the required Distance
        $_mtxVal = $this->_distanceMatrix[$_mtxKey];

    	// Get the rate for the current Cart
    	$_sizeClass = $this->_getSizeClass($data['orderDimension'],$data['weight']);

    	//Get the Mail-bin shipping rate from the open file
		$_basicRates = Mage::getResourceModel($this->priceList)->__get('_basicRates');

        // Get the rate for the given distance and Size class
        $rate = $_basicRates[$_mtxVal][$_sizeClass];

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
            '熊本県'    => 'K',
            '大分県'    => 'K',
            '宮崎県'    => 'K',
            '鹿児島県'  => 'K',
            '沖縄県'    => 'L'
        );
		// If the value is available return it
        return (isset($_zoneMap[$key])) ? $_zoneMap[$key] : FALSE;
    }
}