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

class IrvineSystems_Awardpoints_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * Getter for Referral Url
     * 
     */
    public function getReferalUrl()
    {
        return $this->_getUrl('awardpoints/');
    }

    /**
     * Adjust the points value according to Math set in Admin
     * 
     * @param $amount (int|float) Amount of points to be processed
     * @return $amount (int|float) Adjusted amount of points
     */
    public function processMathValue($amount){

		// Get Config math_method setting
		$math = Mage::getModel('awardpoints/awardpoints')->getConfig('math_method');

		// Adjust the amount according to the method selected
		switch ($math) {
		    case 0:
	            $amount = floor($amount);
	        break;
		    case 1:
	            $amount = round($amount);
	        break;
		}
		// Return the new amount
        return $amount;
    }

    /**
     * Get the amount of points for the product
     * 
     * @param $product (Varien Object) Product Information
     * @return int|float Product points amount
     */
    public function getProductPoints($product){

		// Get product final Price
		$_finalPriceInclTax  = Mage::helper('tax')->getPrice($product, $product->getFinalPrice(), true);
		$_weeeTaxAmount = Mage::helper('weee')->getAmount($product);
		
		$price = $_finalPriceInclTax+$_weeeTaxAmount;

		// Get Money to point conversion factor
		$money_to_points = Mage::getModel('awardpoints/awardpoints')->getConfig('money_points');

		// Calculate the points according to product price
		$productPoints = $price * $money_to_points;
		
		// Check if there is any additional points from Catalog Rules
        $rulesPoints = Mage::getModel('awardpoints/catalogrules')->getCatalogRulesPoints($product);

		// Return null if the points should not be processed
		if(is_string($rulesPoints)) return null;

		// Process Math method on total amount of points
		$productPoints = $this->processMathValue($productPoints + (int)$rulesPoints);

		// check points limits
		$productPoints = $this->checkMaxAmounts($productPoints);

		// if the points are correct return the points
		return $productPoints;
    }

    /**
     * Convert the given amount of money into points
     * 
     * @param $money (float|int) amount to be converted
     * @return int|float Total amount of Points
     */
    public function convertMoneyToPoints($money){
		// Get point to money conversion factor
        $points_money = (int)Mage::getModel('awardpoints/awardpoints')->getConfig('points_money');
		// Process Math method on total amount of Money
        $points_amount = $this->processMathValue($money*$points_money);
		// Return the money amount
        return $points_amount;
    }

    /**
     * Get the Discount value for the given amount of point
     *
     * @param $points (float|int) amount of points to be validated and converted
     * @return int|float Total amount of discount 
     */
	public function getDiscountValue($points){

		// Validate the points
		$pointsValid = $this->validatePoints($points);

		// reset the discount amount value
        $discount_amount = 0;

		// process discount if the points are valid
		if ($pointsValid)
		{
			// Get point to money conversion factor
	        $points_money = (int)Mage::getModel('awardpoints/awardpoints')->getConfig('points_money');
			// Process Math method on total amount of Money
	        $discount_amount = $this->processMathValue($points/$points_money);
		}
		
		// Returtn the Discount ammount
        return $discount_amount;
    }

    /**
     * Validate the passed amount of point if they can be use and converted in discount
     *
     * @param $points (float|int) amount of points to be validated
     * @return bool
     */
	public function validatePoints($points){
		
        // Get Customer Id
		$customerId = Mage::getModel('customer/session')->getCustomerId();
        
        // Set model
		$model = Mage::getModel('awardpoints/account');

        // Get current customer amount of money
		$current = $model->getPointsCurrent($customerId, Mage::app()->getStore()->getId());

        // Check if the amount of point is above the cutomer total points
        if ($current < $points) {
			// if it is inform the customer and unvalidate the points
            Mage::getSingleton('checkout/session')->addError(Mage::helper('awardpoints')->__('Not enough points available.'));
            Mage::helper('awardpoints/event')->setCreditPoints(0);
            return false;
        }
        
        // Check if we are using the automatic maximum amount
        $maxPointsUse = Mage::getModel('awardpoints/awardpoints')->getConfig('full_use');
        if($maxPointsUse) return true;
		
        // Get config values for points stepper
        $step = (int)Mage::getModel('awardpoints/awardpoints')->getConfig('step_value');
        $step_apply = (int)Mage::getModel('awardpoints/awardpoints')->getConfig('step_apply');
        
        // Check if the stepper is enable and if the amount of point will reach the first step level
		if ($step > $points && $step_apply){
			// if not inform the customer and unvalidate the points
            Mage::getSingleton('checkout/session')->addError(Mage::helper('awardpoints')->__('The minimum required points is not reached.'));
            Mage::helper('awardpoints/event')->setCreditPoints(0);
            return false;
        }
        
        // Check if the amount of point is square compare to the steps
		if ($step_apply && ($points % $step) != 0){
			// if not inform the customer and unvalidate the points
			Mage::getSingleton('checkout/session')->addError(Mage::helper('awardpoints')->__('Amount of points wrongly used.'));
			Mage::helper('awardpoints/event')->setCreditPoints(0);
			return false;
		}
		
        // If the points are valid return true
		return true;
	}

    /**
     * Get the amount of points for the Order
     * 
     * @param $order (Varien Object) Order Information
     * @return int|float order points amount
     */
    public function getPointsOnOrder($order = null){

		// Check if we have a order object and get all items
		if ($order){
			$items = $order->getAllItems();
		}else{
			// if we dont have a order we get all items from the Cart
			$cartHelper = Mage::helper('checkout/cart');
			$items = $cartHelper->getCart()->getItems();
		}

		// Reset the Cart amount and points
        $cartPoints = 0;
        
		// Calculation Method
		$pointsMethod = Mage::getModel('awardpoints/awardpoints')->getConfig('cart_method');
		
		// Proicess all order items
		foreach ($items as $item){
			// Get product Id
            $_product = Mage::getModel('catalog/product')->load($item->getProductId());

			// Skip congigurable products
			if ($_product->getVisibility() == 1) continue;
			
			// Get the quantity of products for the current Row
			// NOTE: Item quantity are defined differently from order and cart
			if ($order == null){
				$qty = $item->getQty();
			}else{
				$qty = $item->getQtyOrdered();
			}

			// Calculate the Order points acording the configured Method
			switch ($pointsMethod) {
			    case 0: // By Product Points Value
					// Get the product points Value and multiply for its quantity
					$productPoints = $this->getProductPoints($_product);
					$productPoints *= $qty;
			        break;
			    case 1: // By Order Subtotal
					// Get Item Values
        		    $baseRowTotal = $item->getBaseRowTotal();
		            $baseTaxAmount = $item->getBaseTaxAmount();
        		    $baseDiscountAmount = $item->getBaseDiscountAmount();
				
					// Get Full Eligible Row Value
        		    $rowValue = $baseRowTotal + $baseTaxAmount - $baseDiscountAmount;

					// Get Money to point conversion factor
					$money_to_points = Mage::getModel('awardpoints/awardpoints')->getConfig('money_points');

					// Calculate the points according for the current Row
					$productPoints = $rowValue * $money_to_points;
			        break;
			}
			
			// Get full row Points amount
			$rowPoints = $productPoints;
			// Add the Current Row points to the CartPoints
			$cartPoints += $rowPoints;
        }
        
		// Get any additional points from the Cart Rules
		$rulesPoints = Mage::getModel('awardpoints/cartrules')->getCartRulesPoints($order);
		
		// Add the Rules points to the total Cart Points
		$cartPoints += (int)$rulesPoints;
		
		// Process Math method on total amount of points
		$cartPoints = $this->processMathValue($cartPoints);

		// check points limits
		$cartPoints = $this->checkMaxAmounts($cartPoints);

		// Return the full amount of points
        return $cartPoints;
    }

    /**
     * Cehck if there is a maximum amount of points for the order
     * if the value is available and the points overpass the amount the maximum amount will be return
     * otherwise the original amount will be return
     * 
	 * @param $points (int|float) amount of points to be compared
     * @return int|float valid amount of points
     */
    public function checkMaxAmounts($points){
		$maxPoints = (int)Mage::getModel('awardpoints/awardpoints')->getConfig('max_point_collect_order');
		if ($maxPoints && $maxPoints < $points) $points = $maxPoints;
		return $points;
	}
}
