<?php
/**
 * Created by Celtic Corporation.
 * User: ndlinh
 * Date: 26/10/2012
 *
 * Copyright ©2012 Celtic Corporation. All Rights Reserved.
 */

class Rack_Point_Block_Adminhtml_Order_Create_Point extends Mage_Adminhtml_Block_Template
{
    /**
     * Get base grand total of current quote
     *
     * @return float
     */
    public function getGrandTotal()
    {
        $quote = $this->getQuote();

        return $quote->getBaseGrandTotal();
    }

    public function getRawBaseGrandTotal()
    {
        $quote = $this->getQuote();
        return (float)$quote->getBaseGrandTotal() + (float)$quote->getPointCurrencyUsed() - (float)$quote->getBaseCodFee();
    }

    /**
     * Get current used point
     *
     * @return int
     */
    public function getUsedPoint()
    {
        $session = $this->_getSession();
        if ($session->getPointUsed()) {
            return $session->getPointUsed();
        } else {
            return 0;
        }
    }

    /**
     * Get current point currency.
     *
     * @return float
     */
    public function getUsedPointCurrency()
    {
        $usedPoint = $this->getUsedPoint();
        $money = Mage::helper('rackpoint')->point2Currency($usedPoint);

        return $money;
    }

    /**
     * Get current balance
     *
     * @return Varien_Object
     */
    public function getCurrentBalance()
    {
        /* @var $balance Rack_Point_Model_Point_Balance */
        $balance = Mage::getSingleton('rackpoint/point_balance');
        $result = $balance->getBalanceOfCurrentCustomer(true, $this->_getCustomerId(), $this->_getWebsiteId());

        return $result;
    }

    /**
     * Get current quote
     *
     * @return Mage_Sales_Model_Quote
     */
    public function getQuote()
    {
        return $this->_getQuote();
    }

    public function formatCurrency($currency)
    {
        return Mage::app()->getStore()->formatPrice($currency);
    }

    public function getMinRequiredPoints()
    {
        $quote = $this->_getQuote();
        $_helper = Mage::helper('rackpoint');

        $total = $_helper->getRealBaseTotal($quote) + $quote->getPointCurrencyUsed();

        $disallowFees = $_helper->getDisallowFees();
        $disallowFees[] = 'cod_fee';
        $address = $quote->getShippingAddress();
        foreach ($disallowFees as $fee) {
            if (strpos($fee, 'shipping') !== false) {
                $total -= $address->getData($fee);
            } elseif($fee !== '') {
                $total -= $quote->getData($fee);
            }
        }

        $requirePoint = $_helper->currency2Point($total);
        if ($requirePoint == 0) {
            $requirePoint = $this->getUsedPoint();
        }

        return $requirePoint;
    }

    /**
     * Retrieve session object
     *
     * @return Mage_Adminhtml_Model_Session_Quote
     */
    protected function _getSession()
    {
        return Mage::getSingleton('adminhtml/session_quote');
    }

    /**
     * Retrieve quote object
     *
     * @return Mage_Sales_Model_Quote
     */
    protected function _getQuote()
    {
        return $this->_getSession()->getQuote();
    }

    /**
     * Return current website id
     * @return int|null|string
     */
    protected function _getWebsiteId() {
        return $this->_getQuote()->getStore()->getWebsiteId();
    }

    /**
     * Return current selected customer id.
     * @return int
     */
    protected function _getCustomerId() {
        return $this->_getQuote()->getCustomerId();
    }
}