<?php

class Rack_Point_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * Check if rack point module is enabled or not.
     * 
     * @return boolean
     */
    public function isEnabled()
    {
        return (Mage::getStoreConfig('rackpoint/config/enable') == 1);
    }
    
    /**
     * Format point
     * 
     * @param mixed $value
     * @return string
     */
    public function formatPoint($value)
    {
        return number_format($value);
    }
    
    /**
     * Return update comment for invoice creating.
     * 
     * @param string $orderId
     * @return string
     */
    public function getCommentOfInvoiceAction($orderId)
    {
        return sprintf(Mage::getStoreConfig('rackpoint/message/invoiced'), $orderId);
    }
    
    /**
     * Return update comment for refund.
     * 
     * @param string $orderId
     * @return string
     */
    public function getCommentOfCreditmemoSubAction($orderId)
    {
        return sprintf(Mage::getStoreConfig('rackpoint/message/creditmemo_received'), $orderId);
    }
    
    /**
     * Return update comment for return point after creating creditmemo
     * 
     * @param string $orderId
     * @return string
     */
    public function getCommentOfCreditmemoAddAction($orderId)
    {
        return sprintf(Mage::getStoreConfig('rackpoint/message/creditmemo_used'), $orderId);
    }
    
    /**
     * Return update comment for order creating.
     * 
     * @param string $orderId
     * @return string
     */
    public function getCommentOfPlaceOrderAction($orderId)
    {
        return sprintf(Mage::getStoreConfig('rackpoint/message/order_placed'), $orderId);
    }
    
    /**
     * Return update comment after cancelling received point in order.
     * 
     * @param string $orderId
     * @return string
     */
    public function getCommentOfCancelReceivedAction($orderId)
    {
        return sprintf(Mage::getStoreConfig('rackpoint/message/order_cancel_received'), $orderId);
    }
    
    /**
     * Return update comment after cancelling used point in order.
     * 
     * @param string $orderId
     * @return string
     */
    public function getCommentOfCancelUsedAction($orderId)
    {
        return sprintf(Mage::getStoreConfig('rackpoint/message/order_cancel_used'), $orderId);
    }
    
    /**
     * Return update comment for updating point of admin.
     * 
     * @return string
     */
    public function getCommentOfAdminAction()
    {
        return Mage::getStoreConfig('rackpoint/message/moderation');
    }
    
    /**
     * Get setting value for point expire period
     * 
     * @return int number of months
     */
    public function getPointExpirePeriod()
    {
        return (int)Mage::getStoreConfig('rackpoint/config/expired');
    }

    public function getPointExpireDate(Zend_Date $date = null)
    {
        if ($date == null) {
            $date = new Zend_Date();
        }
        $expired = $this->getPointExpirePeriod();
        $date->add($expired, Zend_Date::MONTH);
        if ($this->isExpiredAtEndMonth()) {
            $lastDayOfMonth = $date->get(Zend_Date::MONTH_DAYS);
            $date->set($lastDayOfMonth, Zend_Date::DAY);
        }

        return $date->toString(Varien_Date::DATETIME_INTERNAL_FORMAT);
    }

    /**
     * @return boolean
     */
    public function isExpiredAtEndMonth() {
        return 1 == (int)Mage::getStoreConfig('rackpoint/config/expired_at_end');
    }
    
    /**
     * Get current conversion rate
     * 
     * @return int
     */
    public function getPointRate()
    {
        return (float)Mage::getStoreConfig('rackpoint/config/rate');
    }
    
    /**
     * Convert point to currency using current rate setting.
     * 
     * @param int $numberOfPoint
     * @param float $rate
     * @return float
     */
    public function point2Currency($numberOfPoint, $rate = null)
    {
        if ($rate == null) {
            $rate = $this->getPointRate();
        }
        $money = (float)($numberOfPoint / $rate);

        return $money;
    }
    
    /**
     * Convert currency to point
     * 
     * @param float $currency
     * @param float $rate
     * @return int
     */
    public function currency2Point($currency, $rate = null)
    {
        if ($rate == null) {
            $rate = $this->getPointRate();
        }
        $point = Mage::helper('rackpoint')->roundPoint($rate * $currency);
        
        return $point;
    }
    
    /**
     * Get description string for point rate.
     * Ex: 100 points = 1Yen
     * 
     * @param int $rate
     * @param int $storeId
     * @return string
     */
    public function getPointRateDescription($rate = null, $storeId = null)
    {
        if ($rate == null) {
            $rate = $this->getPointRate();
        }
        
        return $this->__('%s point(s) = 1 %s', $rate, Mage::app()->getStore($storeId)->getBaseCurrencyCode());
    }

    /**
     * Return receive point value for register new account.
     * 
     * @return int
     */
    public function getPointForRegister()
    {
        return (int)Mage::getStoreConfig('rackpoint/config/register_points');
    }
    
    /**
     * Return receive point value for subscribe newsletter
     * 
     * @return int
     */
    public function getPointForNewsletter()
    {
        return (int)Mage::getStoreConfig('rackpoint/config/newsletter_points');
    }
    
    /**
     * Return receive point value for approved review.
     * 
     * @return int
     */
    public function getPointForReview()
    {
        return (int)Mage::getStoreConfig('rackpoint/config/review_points');
    }
    
    /**
     * Get receive point percent if using "Per Order" mode
     * 
     * @return int
     */
    public function getPointPercent($store = null)
    {
        return (int)Mage::getStoreConfig('rackpoint/calculate/point_percent', $store);
    }
    
    /**
     * Check point calculate mode. "Per Order" or "Per Product".
     * 
     * @return boolean
     */
    public function isPerOrderMode()
    {
        return (Rack_Point_Model_Source_Mode::RECEIVE_MODE_ORDER == Mage::getStoreConfig('rackpoint/calculate/receive_mode'));
    }
    
    /**
     * Check for excluding shipping fee when calculate point in "Per Order" mode.
     * 
     * @return boolean
     */
    public function isExcludeShippingFee()
    {
        return (1 == Mage::getStoreConfig('rackpoint/calculate/exclude_shippingfee'));
    }
    
    /**
     * Check for excluding used point when calculate point in "Per Order" mode.
     * 
     * @return boolean
     */
    public function isExcludeUsedPoint()
    {
        return (1 == Mage::getStoreConfig('rackpoint/calculate/exclude_usedpoint'));
    }
    
    /**
     * Round points depend on round mode.
     * 
     * @param float $point 
     * @return int
     */
    public function roundPoint($point, $digit=0)
    {
        $mode = (int)Mage::getStoreConfig('rackpoint/calculate/round_mode');
        if ($mode == Rack_Point_Model_Source_Round::ROUND_MODE_CEIL) {
            return ceil($point);
        } elseif ($mode == Rack_Point_Model_Source_Round::ROUND_MODE_FLOOR) {
            return floor($point);
        }
        
        return $this->fixStupidRound($point, $digit);
    }
    
    /**
     * Return minimum required for using point
     *
     * @return int
     */
    public function getMinRequiredPoint()
    {
        return (int)Mage::getStoreConfig('rackpoint/config/min_required_point');
    }
    
    /**
     * Get fees that excludes from point calculation.
     * 
     * @return array
     */
    public function getExcludeFees()
    {
        $excludeFees = (string)Mage::getStoreConfig('rackpoint/calculate/exclude_fees');
        
        return explode("\n", $excludeFees);
    }

    /**
     * Get fees that disallowed for customer pay by point.
     *
     * @return array
     */
    public function getDisallowFees()
    {
        $disallowFees = (string)Mage::getStoreConfig('rackpoint/calculate/exclude_used_fees');

        return explode("\n", $disallowFees);
    }

    /**
     * Return used point pay mode
     *
     * @return int
     */
    public function getUsedPointPayMode()
    {
        return (int)Mage::getStoreConfig('rackpoint/calculate/usedpoint_mode');
    }

    /**
     * Check if receive point need to be activated after placing order.
     *
     * @return bool
     */
    public function isActivePointOnPlacing()
    {
        $config = (int)Mage::getStoreConfig('rackpoint/calculate/receive_active_mode');
        return ($config == Rack_Point_Model_Source_Active::ORDER);
    }

    /**
     * Calculate receive point for quote items
     *
     * @param $quoteItem
     * @param $round (Optional)
     * @return int
     */
    public function calculatePoint($quoteItem, $round = true)
    {
        $value = 0;
        $type = $quoteItem->getProduct()->getPointType();
        $amount = $quoteItem->getProduct()->getPointAmount();
        if ($type == Rack_Point_Model_Catalog_Rule::PERCENT) {
            $value = $this->calculatePointPercent($quoteItem, $amount, $round);
        } elseif ($type == Rack_Point_Model_Catalog_Rule::FIXED) {
            $value = $amount;
        }

        return $value;
    }

    public function calculatePointForQuote($quoteItem, $amount, $type)
    {
        $value = 0;
        if ($type == Rack_Point_Model_Catalog_Rule::PERCENT) {
            $value = $this->calculatePointPercent($quoteItem, $amount, false);
        } elseif ($type == Rack_Point_Model_Catalog_Rule::FIXED) {
            $value = $amount;
        }

        return $value;
    }

    public function calculatePointPercent($item, $amount, $round = true)
    {
        $calculation = Mage::getStoreConfig('rackpoint/calculate/prod_includes_tax') == '1' ? true : false;
        if ($calculation == true) {
            $price = $item->getBasePriceInclTax();
        } else {
            $price = $item->getBasePrice();
        }

        if ($round == true) {
            $value = $this->roundPoint($price * ($amount/100));
        } else {
            $value = $price * ($amount/100);
        }

        return $value;
    }

    /**
     * Get maximum value that user can pay via point
     * @param Mage_Sales_Model_Quote $quote
     * @param boolean $isPoint
     * @return float|mixed
     */
    public function getRequirePoints(Mage_Sales_Model_Quote $quote, $isPoint)
    {
        $total = $this->getRealBaseTotal($quote) + $quote->getPointCurrencyUsed();

        $disallowFees = $this->getDisallowFees();
        $address = $quote->getShippingAddress();
        foreach ($disallowFees as $fee) {
            if (strpos($fee, 'shipping') !== false) {
                $total -= $address->getData($fee);
            } elseif($fee !== '') {
                $total -= $quote->getData($fee);
            }
        }
        if ($isPoint == true) {
            return $this->currency2Point($total);
        } else {
            return $total;
        }
    }

    /**
     * Fix PHP round
     * @param $number
     * @return float
     */
    function fixStupidRound($number) {
        $precision = strlen($number - floor($number)) - 2;
        for ($i = $precision; $i >= 0; $i--) {
            $number = round($number, $i);
        }

        return  $number;
    }

    /**
     * Convert Base Currency to Store Currency
     * @param float $amount Amount to convert
     * @param int $currencyCode Currency code(JPY, USD) or Store Id
     * @return float
     */
    public function convertCurrency($amount, $currencyCode = null)
    {
        $baseCurrency = Mage::app()->getStore()->getBaseCurrencyCode();
        if (is_numeric($currencyCode)) {
            $currencyCode = Mage::app()->getStore($currencyCode)->getCurrentCurrencyCode();
        } else if ($currencyCode == null) {
            $currencyCode = Mage::app()->getStore()->getCurrentCurrencyCode();
        }

        if ($baseCurrency === $currencyCode) {
            return $amount;
        }

        $price = Mage::helper('directory')->currencyConvert($amount, $baseCurrency, $currencyCode);

        return $price;
    }

    /**
     * Get total before quote calculated.
     * @param Mage_Sales_Model_Quote $quote
     * @return float
     */
    public function getRealBaseTotal(Mage_Sales_Model_Quote $quote)
    {
        $baseShippingAmount = (float)$quote->getBaseShippingAmount();
        if ($baseShippingAmount <= 0.0001) {
            if ($quote->isVirtual()) {
                return $quote->getBillingAddress()->getBaseGrandTotal();
            } else {
                return $quote->getShippingAddress()->getBaseGrandTotal();
            }
        }

        return $quote->getBaseGrandTotal();
    }
}
