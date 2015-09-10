<?php
/*
 * Irvine Systems Award Points
 *
 * NOTICE OF LICENSE
 * This source file is subject to the Revised BSD License which is bundled with
 * this package in the files LICENSE.txt and LICENSE.html.
 *
 * @category	Magento Sale Extension
 * @package		IrvineSystems_AwardPoints
 * @author Irvine Systems, Inc.
 * @copyright Copyright (c) 2015, Irvine Systems, Inc. (http://irvinesystems.co.jp/)
 * @license     Revised BSD License
 */

class IrvineSystems_Awardpoints_Block_Coupon extends Mage_Checkout_Block_Cart_Abstract
{
    /**
     * Getter for isPointsEnable Config
     * 
     * @return int
     */
    public function isPointsEnable()
	{
        return Mage::getModel('awardpoints/awardpoints')->getConfig('shopping_show');
    }

    /**
     * Getter for AutoUse Config
     * 
     * @return int
     */
    public function getAutoUse()
	{
        return Mage::getModel('awardpoints/awardpoints')->getConfig('auto_use');
    }

    /**
     * Getter for Points on Order
     * 
     * @return int
     */
    public function getPointsOnOrder()
	{
        return Mage::helper('awardpoints/data')->getPointsOnOrder();
    }

    /**
     * Getter for Customer Id
     * 
     * @return int
     */
    public function getCustomerId()
	{
        return Mage::getModel('customer/session')->getCustomerId();
    }

    /**
     * Getter for Store Id
     * 
     * @return int
     */
    public function getStoreId()
	{
        return Mage::app()->getStore()->getId();
    }

    /**
     * Getter for Customer Id
     * 
     * @return int
     */
    public function getPointsCurrentlyUsed()
	{
        return Mage::helper('awardpoints/event')->getCreditPoints();
    }

    /**
     * Getter for Coupon Code Status
     * 
     * @return int
     */
    public function couponCodeDisable()
	{
        return Mage::getModel('awardpoints/awardpoints')->getConfig('coupon_codes');
    }

    /**
     * Getter for Customer Points
     * 
     * @return int
     */
    public function getCustomerPoints()
	{
        $model = Mage::getModel('awardpoints/account');
        return $model->getPointsCurrent($this->getCustomerId(), $this->getStoreId());
    }

    /**
     * Getter for Cart Amount
     * 
     * @return int|float
     */
    public function getCartAmount()
	{
		// Get the minimum subtotal for the order
        $minSubt	= Mage::getModel('awardpoints/awardpoints')->getConfig('min_subtotal');
		// Get the Order Value for points
        $orderValue	= $this->getQuote()->getBaseSubtotal() - $minSubt;
		// Process Admin selected Math
        $cartAmount	= Mage::helper('awardpoints/data')->processMathValue($orderValue);
		// Return the calculated amount
        return $cartAmount;
    }

    /**
     * Getter for Point Value
     * 
     * @return int || float
     */
    public function getPointsValue()
	{
		// Get Base and Currencies
		$baseCurrencyCode = Mage::app()->getStore()->getBaseCurrencyCode();
		$currentCurrencyCode = Mage::app()->getStore()->getCurrentCurrencyCode();
		// Get points factor
		$points = Mage::getStoreConfig('awardpoints/general/points_money');
		// Retrieve the amount of step value in base currency
		if($points >0){
			$points = Mage::getStoreConfig('awardpoints/general/step_value')/$points;
		}else{
			$points = Mage::getStoreConfig('awardpoints/general/step_value')*$points;
		}
		// Convert the amount in current currency
		$point_value = Mage::helper('directory')->currencyConvert($points, $baseCurrencyCode, $currentCurrencyCode);
		// return the amount
		return $point_value;
	}
	
    /**
     * Getter for Minimum Subtotal Value
     * 
     * @return int || float
     */
    public function getMinimumSubtotal()
	{
		// Get Base and Currencies
		$baseCurrencyCode = Mage::app()->getStore()->getBaseCurrencyCode();
		$currentCurrencyCode = Mage::app()->getStore()->getCurrentCurrencyCode();
		// Get Minimum subtotal value
		$min_subtotal = Mage::getStoreConfig('awardpoints/shopping/min_subtotal');
		// if the value is valid convert it in current currency othervise set it to 0
		if($min_subtotal && $min_subtotal > 0){
			$min_subtotal = Mage::helper('directory')->currencyConvert($min_subtotal, $baseCurrencyCode, $currentCurrencyCode);
			}else{
			$min_subtotal = 0;
		}
		// return the amount
		return $min_subtotal;
	}

    /**
     * Getter for Point Information
     * 
     * @return array
     */
    public function getPointsInfo()
	{
        // Get Points Information
        $customerPoints	= $this->getCustomerPoints();
        $points_money	= Mage::getModel('awardpoints/awardpoints')->getConfig('points_money');
        $step			= Mage::getModel('awardpoints/awardpoints')->getConfig('step_value');
        $step_apply		= Mage::getModel('awardpoints/awardpoints')->getConfig('step_apply');
        $full_use		= Mage::getModel('awardpoints/awardpoints')->getConfig('full_use');
        $cartAmount		= $this->getCartAmount();
        $orderSubTotal	= $this->getQuote()->getSubtotal();

		// Calculate Maximum Points use
		$max_use = min(Mage::helper('awardpoints/data')->convertMoneyToPoints($cartAmount), $customerPoints);
		
		// Set returning query values
		$pointsInfo = array(
			'customer_points'	=> $customerPoints,
			'points_money'		=> $points_money,
			'step'				=> $step,
			'step_apply'		=> $step_apply,
			'full_use'			=> $full_use,
			'max_use'			=> $max_use,
			'order_subtotal'	=> $orderSubTotal);

		// Return Points Information
        return $pointsInfo;
    }

    /**
     * Getter for Points to Add Options
     * 
     * @return string
     */
    public function pointsToAddOptions($customer_points, $step)
	{
        // set working parameters
		$toHtml = '';
        $creditToBeAdded = 0;

        // Get Confign Information
        $points_money = Mage::getModel('awardpoints/awardpoints')->getConfig('points_money');
        $max_points_tobe_used = Mage::getModel('awardpoints/awardpoints')->getConfig('max_point_used_order');
        $minSubt = Mage::getModel('awardpoints/awardpoints')->getConfig('min_subtotal');
        $cartAmount		= $this->getCartAmount();

        // Keep a copy of customer original points
		$customer_points_origin = $customer_points;

        // Create option for all customer points
        while ($customer_points > 0){
            // Increase the credit counter for each step processed
			$creditToBeAdded += $step;
            // Decrease the Customer credit counter for each step processed
            $customer_points -= $step;
			// Check if a new step can be added
            if ($creditToBeAdded > $customer_points_origin ||
				$cartAmount < $creditToBeAdded ||
				($max_points_tobe_used != 0 && $max_points_tobe_used < $creditToBeAdded)){
                break;
            }
            // Add the Option to the Html script
            $toHtml .= '<option value="'. $creditToBeAdded .'">'. $this->__("%d points",$creditToBeAdded) .'</option>';
        }
        // Return the HTML to be added
		return $toHtml;
    }
}