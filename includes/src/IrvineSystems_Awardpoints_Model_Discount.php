<?php
/*
 * Irvine Systems Award Points
 *
 * NOTICE OF LICENSE
 * This source file is subject to the Revised BSD License which is bundled with
 * this package in the files LICENSE.txt and LICENSE.html.
 *
 * @category    Magento Sale Extension
 * @package        IrvineSystems_AwardPoints
 * @author Irvine Systems, Inc.
 * @copyright Copyright (c) 2015, Irvine Systems, Inc. (http://irvinesystems.co.jp/)
 * @license     Revised BSD License
 */

class IrvineSystems_Awardpoints_Model_Discount extends Mage_Core_Model_Abstract
{
	// Class parameters
    protected $_discount;
    protected $_quote;
    protected $_couponCode;

    /**
     * Get the Cart Amount
     * 
     * @return float Total Cart Amount in Base Currency
     */
    public function getCartAmount(){
		// Get Cart Values
		$subtotalPrice = Mage::getModel('checkout/session')->getQuote()->getBaseSubtotal();
		$_shippingTax = Mage::getModel('checkout/session')->getQuote()->getShippingAddress()->getBaseTaxAmount();
		$_billingTax = Mage::getModel('checkout/session')->getQuote()->getBillingAddress()->getBaseTaxAmount();
		// Calculate Taxes
		$tax = $_shippingTax + $_billingTax;
		// Calculate Cart Total
        $cartTotal = $subtotalPrice + $tax;
        return $cartTotal;
    }

    /**
     * Compare given points and Cart total points for
	 * retrieve the maximum amount of points to be used
     * 
     * @param int|float $points Points to be compared
     * 
     * @return int|float Max point 
     */
    public function checkMaxPointsToApply($points){
		// Helper instance
		$helper = Mage::helper('awardpoints/data');
		// Get Cart Valid Total
        $cartTotal = $this->getCartAmount();
		// Calculate Cart Total Points
        $cartPoints = $helper->processMathValue($cartTotal);
		// Get lower value whitin given Points and Cart Points
        $maxpoints = min($helper->convertMoneyToPoints($cartPoints), $points);
		// Return Maximum amount
        return $maxpoints;
    }

    /**
     * Apply the Discount to the Whole Cart and update Discount information
     * 
     * @param Mage_Sales_Model_Quote_Item_Abstract $item
     */
    public function apply(Mage_Sales_Model_Quote_Item_Abstract $item)
    {
		// Get current customer credit points
		$points_apply = (int) Mage::helper('awardpoints/event')->getCreditPoints();

        // Keep an instance of the original Quote ans set the quote
        $this->_quote = $quote = $item->getQuote();

        // Get Customer and customer id
        $customer     = $quote->getCustomer();
        $customerId   = $customer->getId();

		// Check if we have points to use and a customer
        if ($points_apply > 0 && $customerId != null) {
            $maxpoints = $this->checkMaxPointsToApply($points_apply);
            if ($points_apply > $maxpoints) {
                $points_apply = $maxpoints;
                Mage::helper('awardpoints/event')->setCreditPoints($points_apply);
            }
		    // Convert the point to be used in a discount amount
			$discountValue = Mage::helper('awardpoints/data')->getDiscountValue($points_apply);

			// Get the current quote address
            $address = $this->_getAddress($item);

			// Instance Customer Session and Cart Helper
			$customerSession = Mage::getSingleton('customer/session');
			$cartHelper = Mage::helper('checkout/cart');
			
			// Check if the discount was already processed
            if (!$this->_discount) {
	            // Get Store Id
                $storeId = Mage::app()->getStore()->getId();
	            // Get Customer total Points
                $clientPoints = Mage::getModel('awardpoints/account')->getPointsCurrent($customerId, $storeId);

	            // Dont process discount If the points to apply are above the Customer total points
                if ($points_apply > $clientPoints) {
                    return false;
                } else {
                    // Otherwise update the discount
					$discounts = $discountValue;
                }

				// Cehck if we still need to update discount
				if (($customerSession->getProductChecked() >= $cartHelper->getSummaryCount() && $discounts > 0) ||
					!$customerSession->getProductChecked() ||
					$customerSession->getProductChecked() == 0)
				{
                    // reset the checked product counter
                    $customerSession->setProductChecked(0);
					// set the left discount
                    $customerSession->setDiscountleft($discountValue);
					// set the class parameter with discount and points amount for not process it anymore
                    $this->_discount   = $discounts;
                    $this->_couponCode = $points_apply;
                } else {
					// set the class parameter with discount difference and points amount for not process it anymore
                    $this->_discount   = $customerSession->getDiscountleft();
                    $this->_couponCode = $points_apply;
                }
            }

            //* Update Discounts amount in current and base currency *//
			// Current currency
            $discountAmount     = 0;
            $discountAmount     = min($item->getRowTotal() - $item->getDiscountAmount(), $quote->getStore()->convertPrice($this->_discount));
            $discountAmount     = min($discountAmount + $item->getDiscountAmount(), $item->getRowTotal());
            $item->setDiscountAmount($discountAmount);

			// Base Currency
            $baseDiscountAmount = 0;
            $baseDiscountAmount = min($item->getBaseRowTotal() - $item->getBaseDiscountAmount(), $this->_discount);
            $baseDiscountAmount = min($baseDiscountAmount + $item->getBaseDiscountAmount(), $item->getBaseRowTotal());
            $item->setBaseDiscountAmount($baseDiscountAmount);

			// Update the customer Session
            $customerSession->setProductChecked($customerSession->getProductChecked() + $item->getQty());
            $customerSession->setDiscountleft($customerSession->getDiscountleft() - $baseDiscountAmount);

            // Update store Discount Labels
            $couponCode   = explode(', ', $address->getCouponCode());
            $couponCode[] = Mage::helper('awardpoints/data')->__('%s credit points', $this->_couponCode);
            $couponCode   = array_unique(array_filter($couponCode));
            $address->setCouponCode(implode(', ', $couponCode));
            $address->setDiscountDescriptionArray($couponCode);
			
        }
    }

    /**
     * Get current active quote instance
     *
     * @return Mage_Sales_Model_Quote
     */
    protected function _getQuote()
    {
        return $this->_getCart()->getQuote();
    }

    /**
     * Retrieve shopping cart model object
     *
     * @return Mage_Checkout_Model_Cart
     */
    protected function _getCart()
    {
        return Mage::getSingleton('checkout/cart');
    }

    /**
     * Get valid address object to be used for discount calculation
     *
     * @param   Mage_Sales_Model_Quote_Item_Abstract $item
     * @return  Mage_Sales_Model_Quote_Address
     */
    protected function _getAddress(Mage_Sales_Model_Quote_Item_Abstract $item)
    {
		// Check which address can be used
		switch (true) {
			// If the item is an istance of a quote address item return the address
			case $item instanceof Mage_Sales_Model_Quote_Address_Item:
				return $item->getAddress();
	        break;
			// If the item is a virtual product return the billing address
			case $item->getQuote()->isVirtual():
				return $item->getQuote()->getBillingAddress();
	        break;
		}
		// If none of the above were true return the shipping address
		return $item->getQuote()->getShippingAddress();
    }
}