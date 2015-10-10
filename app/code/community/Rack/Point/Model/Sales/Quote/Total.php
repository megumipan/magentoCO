<?php

class Rack_Point_Model_Sales_Quote_Total extends Mage_Sales_Model_Quote_Address_Total_Abstract
{
    public function collect(Mage_Sales_Model_Quote_Address $address)
    {
        if (Mage::helper('rackpoint')->isEnabled() == false) {
            return $this;
        }

        $this->_setAddress($address);
        $quote = $address->getQuote();

        if ($quote->getCheckoutMethod(true) == Mage_Checkout_Model_Type_Onepage::METHOD_GUEST) {
            $this->_reset($address);
            return $this;
        }

        if ($quote->getPointsTotalReseted() == false) {
            $this->_reset($address);
        }

        if($this->_getSession()->getPointUsed() == false ) {
            $quote->setPointUsed(0);
            $quote->setPointCurrencyUsed(0);
        }

        $quote->setPointRate(Mage::helper('rackpoint')->getPointRate());
        if($quote->getPointUsed() > 0) {
            $quote->setIsUsingPoints(true);
        }
        //process used points
        if ($quote->getPointsTotalReseted() == true && $address->getBaseGrandTotal()
            && $quote->getCustomer()->getId() && $quote->getIsUsingPoints() && $quote->getPointCurrencyUsed() == 0) {
            $pointInstance = $quote->getPointInstance();
            if (!$pointInstance || !$pointInstance->getId()) {
                $pointInstance = Mage::getModel('rackpoint/point_balance')->loadByCustomerId();
            }
            $usedPoint = min($quote->getPointUsed(), $pointInstance->getBalance());
            $pointCurrencyUsed = Mage::helper('rackpoint')->point2Currency($usedPoint);
            $pointCurrencyUsed = min($address->getBaseGrandTotal(), $pointCurrencyUsed);

            $realBaseGrandTotal = Mage::helper('rackpoint')->getRealBaseTotal($quote);
            $converted = 0;

            if ($pointCurrencyUsed >= $realBaseGrandTotal) {
                $pointCurrencyUsed = $realBaseGrandTotal;
                $converted = Mage::helper('rackpoint')->convertCurrency($pointCurrencyUsed);
            } else {
                $converted = Mage::helper('rackpoint')->convertCurrency($pointCurrencyUsed);
//                $baseTax = $this->_getDiscountedTax($pointCurrencyUsed, $address->getBaseGrandTotal(), $address->getBaseTaxAmount());
//                $tax     = $this->_getDiscountedTax($converted, $address->getGrandTotal(), $address->getTaxAmount());
//                $address->setBaseTotalAmount('tax', $baseTax);
//                $address->setTotalAmount('tax', $tax);
            }

            $address->setTotalAmount('point_used', -$converted);
            $address->setBaseTotalAmount('point_used', -$pointCurrencyUsed);
            $quote->setPointCurrencyUsed($pointCurrencyUsed);
            $address->setPointCurrencyUsed($pointCurrencyUsed);

            //$points = Mage::helper('rackpoint')->currency2Point($pointCurrencyUsed);
            $quote->setPointUsed($usedPoint);
            $address->setPointUsed($usedPoint);
            $address->setGrandTotal(array_sum($address->getAllTotalAmounts()));
            $address->setBaseGrandTotal(array_sum($address->getAllBaseTotalAmounts()));
        }
        
        //process receive points
        if ($quote->getPointsTotalReseted() == true
            && ($address->getAddressType() == Mage_Sales_Model_Quote_Address::TYPE_SHIPPING
                || ($address->getAddressType() == Mage_Sales_Model_Quote_Address::TYPE_BILLING && $quote->getIsVirtual() == true))) {
            $receivedPoint = 0;
            /* @var $_helper Rack_Point_Helper_Data */
            $_helper = Mage::helper('rackpoint');
            if ($_helper->isPerOrderMode()) {
                $calculation = Mage::getStoreConfig('rackpoint/calculate/order_includes_tax') == '1' ? true : false;
                $pointPercent = $_helper->getPointPercent() / 100;
                $grandTotal = $address->getBaseGrandTotal();
                if ($calculation == false) {
                    $grandTotal -= $address->getBaseTaxAmount();
                }
                $grandTotal = max(0, $grandTotal);
                $excludeFees = $_helper->getExcludeFees();
                foreach ($excludeFees as $fee) {
                    $fee = trim($fee);
                    if (!empty($fee)) {
                        $grandTotal = $grandTotal - (float)$address->getData($fee);
                    }
                }
                $receivedPoint = max(0, $_helper->roundPoint($grandTotal * $pointPercent));
                $quote->setReceivePointMode(Rack_Point_Model_Source_Mode::RECEIVE_MODE_ORDER);
            } else {
                $discountCalculationInclTax = Mage::getStoreConfig('tax/calculation/discount_tax') == 1 ? true : false;
                if ($discountCalculationInclTax == true) {
                    $subtotal = $address->getData('base_subtotal_incl_tax');
                    $discountTotal = $subtotal + $address->getBaseDiscountAmount();
                } else {
                    $subtotal = $address->getData('base_subtotal');
                    $discountTotal = $subtotal + $address->getBaseDiscountAmount();
                }

                if ($subtotal > 0 && $discountTotal > 0) {
                    //If customer has using point to pay, used point is use to pay for extra fees (shipping fee, giftwrapping, etc) before
                    //pay for product.
                    $excludeFees = $_helper->getExcludeFees();
                    $disallowFees = $_helper->getDisallowFees();

                    $totalExcludeFee = 0;
                    foreach ($excludeFees as $fee) {
                        $fee = trim($fee);
                        if (!empty($fee) && !in_array($fee, $disallowFees)) {
                            $totalExcludeFee += (float)$address->getData($fee);
                        }
                    }

                    $usedPointCurrency = (float)$address->getPointCurrencyUsed();
                    if ($_helper->getUsedPointPayMode() == Rack_Point_Model_Source_Paymode::PAYMODE_EXTFEE_FIRST) {
                        $usedPointCurrency = max($usedPointCurrency - $totalExcludeFee, 0);
                    }
                    $discountTotal = $discountTotal - $usedPointCurrency;
                    $pointPercent = min($discountTotal/$subtotal, 1);
                    $totalItem = count($address->getAllVisibleItems());
                    $i = 1;
                    $decimalValue = 0;
                    $pointInfo = array();
                    foreach ($address->getAllVisibleItems() as $item) {
                        $pointInfoItem = new Varien_Object();
                        $pointDetails = $item->getProduct()->getPointDetails();
                        $pointInfoItem->setPointType($pointDetails);
                        $pointInfoItem->setProductName($item->getName());

                        $pointData = $item->getProduct()->getPointDetails();
                        $pointValue = 0;
                        $beforeDiscountPoint = 0;
                        if (is_array($pointData)) {
                            foreach ($pointData as $type => $data) {
                                if ($type == Rack_Point_Model_Catalog_Rule::FIXED) {
                                    $pointValue += $_helper->calculatePointForQuote($item, $data['amount'], $type);
                                    $beforeDiscountPoint = $pointValue;
                                } else {
                                    $itemPoint = $_helper->calculatePointForQuote($item, $data['amount'], $type);
                                    $pointValue += ($itemPoint * $pointPercent);
                                    $beforeDiscountPoint += $itemPoint;
                                }
                            }
                        }

                        $assignPoint = $pointValue * $item->getQty();
                        $pointInfoItem->setPointAmount($item->getProduct()->getPointAmount());
                        $pointInfoItem->setPointBeforeDiscount($beforeDiscountPoint * $item->getQty());
                        $pointInfoItem->setRowTotal($item->getPriceInclTax() * $item->getQty());

                        $pointInfoItem->setPointReceived(max(0, $assignPoint));
                        if ($i == $totalItem) {
                            $assignPoint += $decimalValue;
                            $assignPoint = $_helper->roundPoint($assignPoint);
                        } else {
                            $decimalValue += $assignPoint - intval($assignPoint);
                            $i++;
                        }
                        $assignPoint = intval($assignPoint);
                        $assignPoint = max(0, $assignPoint); //in case of used point over subtotal
                        $receivedPoint += $assignPoint;


                        $item->setPointReceived($assignPoint);

                        $pointInfo[] = $pointInfoItem;
                    }
                    
                    $quote->setReceivePointMode(Rack_Point_Model_Source_Mode::RECEIVE_MODE_PRODUCT);
                    $quote->setPointCalculationInfo($pointInfo);
                }
            }

            $address->setPointReceived($receivedPoint);
            $quote->setPointReceived($receivedPoint);
            $pointUsed = $address->getPointUsed();
            $quote->setPointUsed($pointUsed);
        }

        return $this;
    }

    public function fetch(Mage_Sales_Model_Quote_Address $address)
    {
        if (Mage::helper('rackpoint')->isEnabled() == false) {
            return $this;
        }
        
        $amount = $address->getPointReceived();
        if ($amount != 0) {
            $address->addTotal(array(
                'code'  => 'point_received',
                'title' => Mage::helper('rackpoint')->__('Point Received'),
                'value' => $amount,
                'area'  => 'footer',
            ))->save();
        }

        if ($address->getPointUsed() != 0 && $address->getQuote()->getIsUsingPoints()) {
            $address->addTotal(array(
                'code'  => 'point_used',
                'title' => Mage::helper('rackpoint')->__('Point Used'),
                'value' => -$address->getPointUsed(),
            ))->save();
        }
        return $this;
    }

    /**
     * Reset point data in address
     *
     * @param Mage_Sales_Model_Quote_Address $address
     */
    protected function _reset(Mage_Sales_Model_Quote_Address $address)
    {
        $address->setBaseGrandTotal($address->getBaseGrandTotal() + $address->getPointCurrencyUsed());
        $address->setGrandTotal($address->getGrandTotal() + $address->getPointCurrencyUsed());
        $address->setPointReceived(0);
        $address->setPointUsed(0);
        $address->getQuote()->setPointCurrencyUsed(0);
        $address->setPointCurrencyUsed(0);
        $address->getQuote()->setPointsTotalReseted(true);
    }

    protected function _getDiscountedTax($point, $total, $tax)
    {
        $mode = Mage::getStoreConfig('tax/classes/point_tax_class');

        if(!$mode) {
            return $tax;
        }

        $calculator = Mage::getSingleton('tax/calculation');
        $request = $calculator->getRateRequest(
            $this->_address,
            $this->_address->getQuote()->getBillingAddress(),
            $this->_address->getQuote()->getCustomerTaxClassId(),
            $this->_address->getQuote()->getStore()
        );
        $request->setProductClassId($mode);

        $rate = $calculator->getRate($request);

        $method = Mage::getStoreConfig('tax/calculation/round');
        $currentCurrency = $this->_address->getQuote()->getQuoteCurrencyCode();

        if($currentCurrency == 'JPY' && $method != '') {
            $result = $method(($total - $tax - $point) * $rate / 100);
        }

        return $result;
    }

    protected function _getSession()
    {
        if(Mage::app()->getStore()->isAdmin()) {
            return Mage::getSingleton('adminhtml/session_quote');
        }

        return Mage::getSingleton('checkout/session');
    }
}