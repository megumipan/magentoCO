<?php

class Rack_Point_Model_Observer 
{
    /**
     * Add point data to product after load
     * 
     * @param Varien_Object $observer
     * @return Rack_Point_Model_Observer
     */
    public function addPointData($observer)
    {
        if ($this->isActive() == false || $this->isPerOrderMode() == true) {
            return $this;
        }
        $product = $observer->getEvent()->getProduct();
        if ($product instanceof Mage_Catalog_Model_Product) {
            Mage::getSingleton('rackpoint/point')->addPointToProduct($product);
        }

        return $this;
    }

    /**
     * Add point data to product collection after load
     * 
     * @param Varien_Object $observer
     * @return Rack_Point_Model_Observer
     */
    public function addPointDataToCollection($observer)
    {
        if ($this->isActive() == false || $this->isPerOrderMode() == true) {
            return $this;
        }
        $productCollection = $observer->getEvent()->getCollection();
        Mage::getSingleton('rackpoint/point')->addPointToCollection($productCollection);

        return $this;
    }

    /**
     * Add point data to quote item collection after load
     *
     * @param Varien_Object $observer
     * @return Rack_Point_Model_Observer
     */
    public function addPointDataToCollectionOfQuote($observer)
    {
        if ($this->isActive() == false || $this->isPerOrderMode() == true) {
            return $this;
        }
        $productCollection = $observer->getEvent()->getProductCollection();
        Mage::getSingleton('rackpoint/point')->addPointToCollection($productCollection);
    }

    /**
     * Set point data to order item on import item
     *
     * @param Varien_Object $observer
     * @return Rack_Point_Model_Observer
     */
    public function salesEventConvertQuoteItemToOrderItem($observer)
    {
        if ($this->isActive() == false || $this->isPerOrderMode() == true) {
            return $this;
        }
        $orderItem = $observer->getEvent()->getOrderItem();
        $item = $observer->getEvent()->getItem();
        //$item->setPointReceived($item->getProduct()->getRackPointValue() * $item->getQty());

        $orderItem->setPointReceived($item->getPointReceived());
        
        return $this;
    }

    /**
     * Set point data to order from quote address
     *
     * @param Varien_Object $observer
     * @return Rack_Point_Model_Observer
     */
    public function salesEventConvertQuoteAddressToOrder($observer)
    {
        if ($this->isActive() == false) {
            return $this;
        }
        $address = $observer->getEvent()->getAddress();
        $order   = $observer->getEvent()->getOrder();
        $order->setPointReceived($address->getPointReceived());
        if ($address->getQuote()->getIsUsingPoints()) {
            $order->setPointUsed($address->getPointUsed());
        }
        
        return $this;
    }

    /**
     * Set point data to order from quote address
     *
     * @param Varien_Object $observer
     * @return Rack_Point_Model_Observer
     */
    public function salesEventConvertQuoteToOrder($observer)
    {
        if ($this->isActive() == false) {
            return $this;
        }
        $quote = $observer->getEvent()->getQuote();
        $order = $observer->getEvent()->getOrder();
        $order->setPointReceived($quote->getPointReceived());
        
        if ($quote->getIsUsingPoints()) {
            $order->setPointUsed($quote->getPointUsed());
        }
        
        $order->setPointRate($quote->getPointRate());
        $order->setReceivePointMode($quote->getReceivePointMode());
        
        return $this;
    }
    
    /**
     * Capture invoice save event to assign point to customer
     * 
     * @param Varien_Object $observer 
     * @return Rack_Point_Model_Observer
     */
    public function invoiceSaveAfter($observer)
    {
        if ($this->isActive() == false) {
            return $this;
        }
        $invoice = $observer->getEvent()->getInvoice();
        
        if ($invoice->getRegisterForInvoicePointReceived() == true) {
            $order = $invoice->getOrder();

            $balance = Mage::getSingleton('rackpoint/point_balance');
            $balance->addPointByInvoice($order->getCustomerId(), $invoice);
        }
        
        return $this;
    }
    
    /**
     * Capture creditmemo save event to return point of customer
     * 
     * @param Varien_Object $observer 
     * @return Rack_Point_Model_Observer
     */
    public function creditMemoSaveAfter($observer)
    {
        if ($this->isActive() == false) {
            return $this;
        }
        $creditMemo = $observer->getEvent()->getCreditmemo();
        $order = $creditMemo->getOrder();

        if ($order->getCustomerIsGuest() || !$order->getCustomerId()) {
            return $this;
        }

        $balance = Mage::getSingleton('rackpoint/point_balance');
        $balance->returnPointByCreditMemo($order->getCustomerId(), $creditMemo);
        
        return $this;
    }

    /**
     * Capture invoice register to set point recieved data
     * 
     * @param Varien_Object $observer 
     * @return Rack_Point_Model_Observer
     */
    public function invoiceRegisterHandler($observer)
    {
        if ($this->isActive() == false) {
            return $this;
        }
        $invoice = $observer->getEvent()->getInvoice();
        $order   = $observer->getEvent()->getOrder();
        //Check guest order
        if (!$order->getCustomerId()) {
            return $this;
        }
        $total   = 0;

        if ($order->getReceivePointMode() == Rack_Point_Model_Source_Mode::RECEIVE_MODE_ORDER) {
            $pointPercent = Mage::helper('rackpoint')->getPointPercent($order->getStoreId()) / 100;
            $grandTotal = $invoice->getBaseGrandTotal();
            $calculation = Mage::getStoreConfig('rackpoint/calculate/order_includes_tax') == '1' ? true : false;
            
            if ($calculation == false) {
                $grandTotal -= $invoice->getBaseTaxAmount();
            }
            $grandTotal = max(0, $grandTotal);
            $excludeFees = Mage::helper('rackpoint')->getExcludeFees();
            foreach ($excludeFees as $fee) {
                $fee = trim($fee);
                if (!empty($fee)) {
                    $grandTotal = $grandTotal - (float)$invoice->getData($fee);
                }
            }
            $total = max(0, $grandTotal) * $pointPercent;
            $total = Mage::helper('rackpoint')->currency2Point($total);                                                                        
        } else {
            foreach ($invoice->getAllItems() as $item) {
                if ($item->isDeleted()) {
                    continue;
                }
                $orderItem = $item->getOrderItem();
                //TODO: check received point with invoiced point. invoiced point can not greater than received point
                $pointReceivedInvoiced = ($orderItem->getPointReceived() / $orderItem->getQtyOrdered()) * $item->getQty();
                $orderItem->setPointReceivedInvoiced($orderItem->getPointReceivedInvoiced() + $pointReceivedInvoiced);
                $item->setPointReceived($pointReceivedInvoiced);

                $total += $pointReceivedInvoiced;
            }
        }

        if ($order->getPointReceivedInvoiced() < $order->getPointReceived()) {
            $order->setPointReceivedInvoiced($order->getPointReceivedInvoiced() + $total);
            $invoice->setPointReceived($invoice->getPointReceived() + $total);
            if ($invoice->getPointReceived() > 0) {
                $invoice->setRegisterForInvoicePointReceived(true);
            }
        }
        
        if ($invoice->getPointUsed() > 0) {
            $order->setPointUsedInvoiced($order->getPointUsedInvoiced() + $invoice->getPointUsed());
        }
        

        return $this;
    }
    
    /**
     * Capture refund event to set refund point
     * 
     * @param Varien_Object $observer
     * @return Rack_Point_Model_Observer 
     */
    public function creditMemoRefundHandler($observer)
    {
        if ($this->isActive() == false) {
            return $this;
        }
        $creditMemo = $observer->getEvent()->getCreditmemo();
        $order = $creditMemo->getOrder();
        $total = 0;
        /* @var $helper Rack_Point_Helper_Data */
        $helper = Mage::helper('rackpoint');
        if ($order->getReceivePointMode() == Rack_Point_Model_Source_Mode::RECEIVE_MODE_ORDER) {
            $order->setPointReceivedRefunded($order->getPointReceivedRefunded() + $creditMemo->getPointReceived());
        } else {
            foreach ($creditMemo->getAllItems() as $item) {
                if ($item->isDeleted()) {
                    continue;
                }
                $orderItem = $item->getOrderItem();
                //TODO: check invoiced point with refunded point. refunded point can not greater than invoiced point
                $pointReceivedRefunded = $helper->roundPoint(($orderItem->getPointReceivedInvoiced() / $orderItem->getQtyInvoiced()) * $item->getQty());
                $orderItem->setPointReceivedRefunded($orderItem->getPointReceivedRefunded() + $pointReceivedRefunded);

                $total += $pointReceivedRefunded;
            }
            
            $order->setPointReceivedRefunded($order->getPointReceivedRefunded() + $total);
            $creditMemo->setPointReceived($creditMemo->getPointReceived() + $total);
        }
        
        $order->setPointUsedRefunded($order->getPointUsedRefunded() + $creditMemo->getPointUsed());
        
        return $this;
    }
    
    /**
     * Enable Zero Subtotal Checkout payment method
     * if customer has enough points to cover grand total
     *
     * @param Varien_Event_Observer $observer
     */
    public function preparePaymentMethod($observer)
    {
        if ($this->isActive() == false) {
            return $this;
        }

        $quote = $observer->getEvent()->getQuote();
        if (!is_object($quote) || !$quote->getId()) {
            return $this;
        }
        
        $pointInstance = $quote->getPointInstance();
        if (!$pointInstance || !$pointInstance->getId()) {
            return $this;
        }
        $baseQuoteGrandTotal = $quote->getBaseGrandTotal();
        //$balance = $pointInstance->getBalanceOfCurrentCustomer(true);

        if ($baseQuoteGrandTotal <= 0.001 && $quote->getPointUsed() > 0) {
            $paymentCode = $observer->getEvent()->getMethodInstance()->getCode();

            $result = $observer->getEvent()->getResult();
            if ('free' === $paymentCode) {
                $result->isAvailable = true;
            } else {
                $result->isAvailable = false;
            }
        }
        
        return $this;
    }
    
    /**
     * Set payment method by "free" if customer has enough points to cover grand total
     * 
     * @param Varien_Event_Observer $observer
     * @return Rack_Point_Model_Observer 
     */
    public function paymentDataImport($observer)
    {
        if ($this->isActive() == false) {
            return $this;
        }
        $input = $observer->getEvent()->getInput();
        $quote = $observer->getEvent()->getPayment()->getQuote();
        $quote->setIsUsingPoints((bool)$input->getUsePoint());
        if ($quote->getIsUsingPoints()) {
            $usedPoint = (int)$input->getUsedPoint();
            if (Mage::app()->getStore()->isAdmin() == false) {
                $minRequiredPoint = Mage::helper('rackpoint')->getMinRequiredPoint();
                if ($minRequiredPoint > 0 && $usedPoint < $minRequiredPoint) {
                    Mage::throwException(Mage::helper('rackpoint')->__('Used point is lower than minimum required point %s.', number_format(Mage::getStoreConfig('rackpoint/config/min_required_point'))));
                }
            }
            $quote->setPointUsed($usedPoint);
            $point = Mage::getModel('rackpoint/point_balance')->loadByCustomerId();
            if ($point->getId()) {
                $quote->setPointInstance($point);
                if (!$input->getMethod()) {
                    $input->setMethod('free');
                }
                Mage::getSingleton('checkout/session')->setPointUsed($input->getUsedPoint());
            } else {
                Mage::getSingleton('checkout/session')->setPointUsed(false);
                $quote->setIsUsingPoints(false);
            }
        } else {
            $quote->setPointCurrencyUsed(0);
            $quote->setPointUsed(0);
            $quote->getShippingAddress()->setPointCurrencyUsed(0);
            $quote->getShippingAddress()->setPointUsed(0);
            Mage::getSingleton('checkout/session')->setPointUsed(false);
        }
        
        return $this;
    }
    
    /**
     * Set flag to reset reward points totals
     *
     * @param Varien_Event_Observer $observer
     * @return Rack_Point_Model_Observer
     */
    public function quoteCollectTotalsBefore(Varien_Event_Observer $observer)
    {
        if ($this->isActive() == false) {
            return $this;
        }
        $quote = $observer->getEvent()->getQuote();
        $quote->setPointsTotalReseted(false);
        if ($this->_getSession()->getCartWasUpdated()) {
            $this->_getSession()->setPointUsed(0);
        }

        return $this;
    }
    
    /**
     * Set forced can creditmemo flag if refunded amount less then invoiced amount of points
     *
     * @param Varien_Event_Observer $observer
     * @return Rack_Point_Model_Observer
     */
    public function orderLoadAfter(Varien_Event_Observer $observer)
    {
        if ($this->isActive() == false) {
            return $this;
        }
        /* @var $order Mage_Sales_Model_Order */
        $order = $observer->getEvent()->getOrder();
        if ($order->canUnhold()) {
            return $this;
        }
        if ($order->isCanceled() ||
            $order->getState() === Mage_Sales_Model_Order::STATE_CLOSED ) {
            return $this;
        }
        if (($order->getPointUsedInvoiced() - $order->getPointUsedRefunded()) > 0
             || ($order->getPointReceivedInvoiced() - $order->getPointReceivedRefunded()) > 0) {
            $order->setForcedCanCreditmemo(true);
        }
        return $this;
    }
    
    /**
     * Substract used point after customer place order
     * 
     * @param Varien_Event_Observer $observer
     * @return Rack_Point_Model_Observer
     */
    public function orderPlaceAfter($observer)
    {
        if ($this->isActive() == false) {
            return $this;
        }
        /* @var $order Mage_Sales_Model_Order */
        $order = $observer->getEvent()->getOrder();

        //process receive point
        if (Mage::helper('rackpoint')->isActivePointOnPlacing()) {
            $order->setPointReceivedInvoiced($order->getPointReceived());
            $order->setRegisterForActiveReceivedPoint(true);
        }

        $usedPoint = $order->getPointUsed();
        if ($usedPoint > 0) {
            $balance = Mage::getModel('rackpoint/point_balance');
            $balance->subPointByPlaceOrder($order);
        }

        if ($order->getQuote() && $order->getQuote()->getCheckoutMethod(true) == Mage_Checkout_Model_Type_Onepage::METHOD_REGISTER) {
            $this->_addCustomerRegisterPoint($order->getCustomerId(), $order->getCustomer()->getWebsiteId());
        }

        Mage::getSingleton('checkout/session')->setPointUsed(0);
        
        return $this;
    }
    
    /**
     * Register cancel points
     * 
     * @param Varien_Event_Observer $observer
     * @return Rack_Point_Model_Observer
     */
    public function orderPaymentCancel($observer)
    {
        if ($this->isActive() == false) {
            return $this;
        }
        $order = $observer->getEvent()->getPayment()->getOrder();
        if ($order) {
            $order->setRegisterForCancelPoint(true);
            if ($order->getPointUsed() > 0) {
                $order->setPointUsedCanceled($order->getPointUsed());
            }
            if ($order->getPointReceivedInvoiced() > 0) {
                $order->setPointReceivedCanceled($order->getReceivedInvoiced());
            }
        }
        
        return $this;
    }
    
    /**
     * Peform cancel points if order is cancelled.
     * 
     * @param Varien_Event_Observer $observer
     * @return Rack_Point_Model_Observer
     */
    public function orderSaveAfter($observer)
    {
        if ($this->isActive() == false) {
            return $this;
        }
        /** @var $order Mage_Sales_Model_Order */
        $order = $observer->getEvent()->getOrder();

        if ($order->getCustomerIsGuest() || !$order->getCustomerId()) {
            return $this;
        }

        /** @var $balance Rack_Point_Model_Point_Balance */
        $balance = Mage::getModel('rackpoint/point_balance');
        if ($order->getRegisterForCancelPoint() == true) {
            $balance->processPointByCancelOrder($order);
        }

        if ($order->getRegisterForActiveReceivedPoint() == true) {
            $balance->updatePoint($order->getCustomerId(),
                                  $order->getStore()->getWebsiteId(),
                                  $order->getPointReceivedInvoiced(),
                                  Mage::helper('rackpoint')->__('Add point after placing order #%s', $order->getIncrementId()),
                                  Mage::helper('rackpoint')->__(Rack_Point_Model_Point_History::ACTION_BY_PLACE_ORDER),
                                  false, //do not send mail
                                  $order->getIncrementId()
                                 );
        }
        
        return $this;
    }

    /**
     * Set points balance to refund before creditmemo register
     *
     * @param Varien_Event_Observer $observer
     * @return Rack_Point_Model_Observer
     */
    public function setPointToRefund(Varien_Event_Observer $observer)
    {
        if ($this->isActive() == false) {
            return $this;
        }
        $input = $observer->getEvent()->getRequest()->getParam('creditmemo');
        $creditmemo = $observer->getEvent()->getCreditmemo();

        if (isset($input['refund_point_enable'])) {
            if($input['refund_point_enable'] == 2) { $input['used_point'] = 0; }
            $enable = $input['refund_point_enable'];
            $balance = (int)$input['used_point'];
            $balance = min($creditmemo->getPointUsed(), $balance);
            //if ($enable && $balance) {
                $creditmemo->setPointUsed($balance);
            //}
        }

        if (isset($input['adjustment_point_received'])) {
            $creditmemo->setPointReceived((int)$input['adjustment_point_received']);
        }
        
        return $this;
    }
    
    /**
     * Update reward points for customer
     * Event: adminhtml_customer_save_after
     *
     * @param Varien_Event_Observer $observer
     * @return Rack_Point_Model_Observer
     */
    public function saveCustomerPoints($observer)
    {
        if ($this->isActive() == false) {
            return $this;
        }

        $request = $observer->getEvent()->getRequest();
        $customer = $observer->getEvent()->getCustomer();
        $data = $request->getPost('rackpoint');
        
        
        if(!$request->getParam('customer_id')){
            $this->customerRegister($observer);
        }
        if ($data) {
            if (!isset($data['website_id'])) {
                if ($customer->getWebsiteId() == 0) {
                    $data['website_id'] = Mage::app()->getDefaultStoreView()->getWebsiteId();
                } else {
                    $data['website_id'] = $customer->getWebsiteId();
                }
            }
            if (!empty($data['points_delta']) && is_numeric($data['points_delta']) && $data['points_delta'] != 0) {
                $balance = Mage::getModel('rackpoint/point_balance');
                $balance->updatePoint($customer->getId(), $data['website_id'], $data['points_delta'], $data['comment'], Rack_Point_Model_Point_History::ACTION_BY_ADMIN);
            }
        }
        return $this;
    }

    public function saveCustomerWhileCreateOrder($observer)
    {
        $customer = $observer->getEvent()->getQuote()->getCustomer();
        $order    = $observer->getEvent()->getOrder();
        if ($customer->getCreatedAt() == $order->getCreatedAt()) {
            //New customer.
            $this->_addCustomerRegisterPoint($customer->getId(), Mage::app()->getStore($customer->getStore()->getId())->getWebsiteId());
        }
    }
    
    /**
     * Update reward points after customer register
     *
     * @param Varien_Event_Observer $observer
     * @return Rack_Point_Model_Observer
     */
    public function customerRegister($observer)
    {
        if ($this->isActive() == false) {
            return $this;
        }
        /* @var $customer Mage_Customer_Model_Customer */
        $customer = $observer->getEvent()->getCustomer();
        try {
            //add point here
            $websiteId =  Mage::app()->getStore($customer->getStore()->getId())->getWebsiteId();
            if (!$websiteId || $websiteId == 0) {
                $websiteId = $customer->getWebsiteId();
            }
            if ($websiteId == 0) {
                $websiteId = Mage::app()->getDefaultStoreView()->getWebsiteId();
            }
            $this->_addCustomerRegisterPoint($customer->getId(), $websiteId);
        } catch (Exception $e) {
            Mage::logException($e);
        }

        return $this;
    }

    /**
     * Add register point to customer account
     * @param $customerId
     * @param $websiteId
     */
    protected function _addCustomerRegisterPoint($customerId, $websiteId)
    {
        $addPoint = Mage::helper('rackpoint')->getPointForRegister();
        if ($addPoint > 0) {
            $balance = Mage::getModel('rackpoint/point_balance');
            $comment = Mage::getStoreConfig('rackpoint/message/customer_register');
            $balance->updatePoint($customerId, $websiteId, $addPoint, Mage::helper('rackpoint')->__($comment), Rack_Point_Model_Point_History::ACTION_BY_REGISTER);
        }
    }
    
    /**
     * Update points balance after first successful subscribtion
     *
     * @param Varien_Event_Observer $observer
     * @return Rack_Point_Model_Observer
     */
    public function customerSubscribed($observer)
    {
        if ($this->isActive() == false) {
            return $this;
        }
        $subscriber = $observer->getEvent()->getSubscriber();
        // add only new subscribtions
        if (!$subscriber->isObjectNew() || !$subscriber->getCustomerId()) {
            return $this;
        }
        
        $addPoint = Mage::helper('rackpoint')->getPointForNewsletter();
        if ($addPoint > 0) {
            $balance = Mage::getModel('rackpoint/point_balance');
            $comment = Mage::getStoreConfig('rackpoint/message/newsletter_subscribed');
            $websiteId = Mage::app()->getStore($subscriber->getStoreId())->getWebsiteId();
            $balance->updatePoint($subscriber->getCustomerId(), $websiteId, $addPoint, Mage::helper('rackpoint')->__($comment), Rack_Point_Model_Point_History::ACTION_BY_NEWSLETTER);
        }

        return $this;
    }

    /**
     * Update points balance after review submit
     *
     * @param Varien_Event_Observer $observer
     * @return Rack_Point_Model_Observer
     */
    public function reviewSubmit($observer)
    {
        /* @var $review Mage_Review_Model_Review */
        $review = $observer->getEvent()->getObject();
        if ($this->isActive() == false) {
            return $this;
        }
        if ($review->isApproved() && $review->getCustomerId()) {
            $addPoint = Mage::helper('rackpoint')->getPointForReview();
            if ($addPoint > 0) {
                $balance = Mage::getModel('rackpoint/point_balance');
                $comment = Mage::getStoreConfig('rackpoint/message/write_review');
                //review for product
                if ($review->getEntityId() == 1) {
                    $product = Mage::getModel('catalog/product')->load($review->getEntityPkValue());
                    $comment = Mage::helper('rackpoint')->__($comment, $product->getSku());
                }

                $websiteId = Mage::app()->getStore($review->getStoreId())->getWebsiteId();
                $balance->updatePoint($review->getCustomerId(), $websiteId, $addPoint, $comment, Rack_Point_Model_Point_History::ACTION_BY_WRITE_REVIEW);
            }
        }

        return $this;
    }
    
    /**
     * Check rules that contains affected attribute
     * If rules were found they will be set to inactive and notice will be add to admin session
     *
     * @param string $attributeCode
     * @return Rack_Point_Model_Observer
     */
    protected function _checkPointRulesAvailability($attributeCode)
    {
        /* @var $collection Rack_Point_Model_Mysql4_Catalog_Rule_Collection */
        $collection = Mage::getResourceModel('rackpoint/catalog_rule_collection')
                          ->addAttributeInConditionFilter($attributeCode);

        $disabledRulesCount = 0;
        foreach ($collection as $rule) {
            $rule->setIsActive(0);
            $this->_removeAttributeFromConditions($rule->getConditions(), $attributeCode);
            $this->_removeAttributeFromConditions($rule->getActions(), $attributeCode);
            $rule->save();

            $disabledRulesCount++;
        }

        if ($disabledRulesCount) {
            Mage::getSingleton('adminhtml/session')->addWarning(
                Mage::helper('salesrule')->__('%d Shopping Cart Price Rules based on "%s" attribute have been disabled.', $disabledRulesCount, $attributeCode));
        }

        return $this;
    }

    /**
     * Remove catalog attribute condition by attribute code from rule conditions
     *
     * @param Mage_Rule_Model_Condition_Combine $combine
     * @param string $attributeCode
     */
    protected function _removeAttributeFromConditions($combine, $attributeCode)
    {
        $conditions = $combine->getConditions();
        foreach ($conditions as $conditionId => $condition) {
            if ($condition instanceof Mage_Rule_Model_Condition_Combine) {
                $this->_removeAttributeFromConditions($condition, $attributeCode);
            }
            if ($condition instanceof Mage_SalesRule_Model_Rule_Condition_Product) {
                if ($condition->getAttribute() == $attributeCode) {
                    unset($conditions[$conditionId]);
                }
            }
        }
        $combine->setConditions($conditions);
    }

    /**
     * After save attribute if it is not used for promo rules already check rules for containing this attribute
     *
     * @param Varien_Event_Observer $observer
     * @return Rack_Point_Model_Observer
     */
    public function catalogAttributeSaveAfter(Varien_Event_Observer $observer)
    {
        $attribute = $observer->getEvent()->getAttribute();
        if ($attribute->dataHasChangedFor('is_used_for_promo_rules') && !$attribute->getIsUsedForPromoRules()) {
            $this->_checkPointRulesAvailability($attribute->getAttributeCode());
        }

        return $this;
    }

    /**
     * After delete attribute check rules that contains deleted attribute
     * If rules was found they will seted to inactive and added notice to admin session
     *
     * @param Varien_Event_Observer $observer
     * @return Rack_Point_Model_Observer
     */
    public function catalogAttributeDeleteAfter(Varien_Event_Observer $observer)
    {
        $attribute = $observer->getEvent()->getAttribute();
        if ($attribute->getIsUsedForPromoRules()) {
            $this->_checkPointRulesAvailability($attribute->getAttributeCode());
        }

        return $this;
    }
    
    /**
     * Apply all rules after import products
     * 
     * @param Varien_Event_Observer $observer
     * @return Rack_Point_Model_Observer 
     */
    public function appyAllRules(Varien_Event_Observer $observer)
    {
        if ($this->isActive()) {
            Mage::getModel('rackpoint/catalog_rule')->applyAll();
        }
        
        return $this;
    }

    public function disableApplyRuleOnProductSave(Varien_Event_Observer $observer)
    {
        Mage::register('disableApplyRuleOnProductSave', true);

        return $this;
    }
    
    /**
     * Apply rules for specific product
     * 
     * @param Varien_Event_Observer $observer
     * @return Rack_Point_Model_Observer 
     */
    public function applyAllRulesOnProduct(Varien_Event_Observer $observer)
    {
        if (!$this->isActive()) {
            return $this;
        }

        if (Mage::registry('disableApplyRuleOnProductSave') === true) {
            Mage::unregister('disableApplyRuleOnProductSave');
            return $this;
        }

        $product = $observer->getEvent()->getProduct();
        if ($product->getIsMassupdate()) {
            return $this; 
        }
        Mage::getModel('rackpoint/catalog_rule')->applyForProduct($product);
            
        return $this;
    }

    /**
     * Apply rules after saving product data in admin
     *
     * Event: controller_action_postdispatch_adminhtml_catalog_product_save
     * @param Varien_Event_Observer $observer
     *
     * @return Rack_Point_Model_Observer
     */
    public function applyAllRulesOnProductForSave(Varien_Event_Observer $observer)
    {
        /**
         * @var $controller Mage_Core_Controller_Varien_Action
         */
        $controller = $observer->getEvent()->getControllerAction();
        $productId = $controller->getRequest()->getParam('id');
        if ($productId) {
            Mage::getModel('rackpoint/catalog_rule')->applyForProduct($productId);
        }

        return $this;
    }

    public function salesOrderCreateProcessData(Varien_Event_Observer $observer)
    {
        if (!$this->isActive()) {
            return $this;
        }

        $data = new Varien_Object($observer->getEvent()->getRequest());
        $input = new Varien_Object($data->getPayment());
        /**
         * @var $orderCreateObject Mage_Adminhtml_Model_Sales_Order_Create
         */
        $orderCreateObject = $observer->getEvent()->getOrderCreateModel();
        $quote = $orderCreateObject->getQuote();
        $quote->setIsUsingPoints((bool)$input->getUsePoint());
        if ($quote->getIsUsingPoints()) {
            $quote->setPointsTotalReseted(true);
            $usedPoint = (int)$input->getUsedPoint();

            $minRequiredPoint = Mage::helper('rackpoint')->getMinRequiredPoint();
            if ($minRequiredPoint > 0 && $usedPoint < $minRequiredPoint) {
                Mage::throwException(Mage::helper('rackpoint')->__('Used point is lower than minimum required point %s.', number_format(Mage::getStoreConfig('rackpoint/config/min_required_point'))));
            }
            $quote->setPointUsed($usedPoint);
            $websiteId = $quote->getStore()->getWebsiteId();
            /* @var $point Rack_Point_Model_Point_Balance */
            $point = Mage::getModel('rackpoint/point_balance')->loadByCustomerId($quote->getCustomerId(), $websiteId);
            if ($point->getId()) {
                $quote->setPointInstance($point);
                if (!$input->getMethod()) {
                    $quote->getPayment()->setMethod('free');
                    $input->setMethod('free');
                }
                $orderCreateObject->getSession()->setPointUsed($input->getUsedPoint());
            } else {
                $orderCreateObject->getSession()->setPointUsed(false);
                $quote->setIsUsingPoints(false);
            }
        } else {
            $orderCreateObject->getSession()->setPointUsed(false);
        }
    }

    public function adminSalesOrderCreateIndex(Varien_Event_Observer $observer)
    {
        /* @var adminSession Mage_Adminhtml_Model_Session_Quote */
        $adminSession = Mage::getSingleton('adminhtml/session_quote');
        $quote = $adminSession->getQuote();
        //$adminSession->setPointUsed(false);
        if ($quote->getPointUsed() > 0) {
            $quote->setIsUsingPoints(true);
            $quote->getPointsTotalReseted(false);
            //$quote->setPointCurrencyUsed(0);
            //$quote->collectTotals();
        }
    }

    /**
     * Handler controller_action_postdispatch_customer_account_createpost to send point update for register.
     * This only send mail if customer has just register.
     *
     * @param Varien_Event_Observer $observer
     */
    public function customerCreatePost(Varien_Event_Observer $observer)
    {
        $helperObject = Mage::registry('register_assign_point');
        if ($helperObject && $helperObject->getMailHelper() && $helperObject->getHistory()) {
            $helperObject->getMailHelper()->sendModeratorUpdateEmail($helperObject->getHistory());
        }
    }

    /**
     * Used to check if module is enable or not
     * 
     * @return boolean
     */
    public function isActive()
    {
        return Mage::helper('rackpoint')->isEnabled();
    }

    /**
     * Used to check if module is enable or not
     * 
     * @return boolean
     */
    public function isPerOrderMode()
    {
        return Mage::helper('rackpoint')->isPerOrderMode();
    }
    
    /**
     * Apply all point rules
     * 
     * @return Rack_Point_Model_Observer
     */
    public function applyAllRules()
    {
        Mage::getModel('rackpoint/catalog_rule')->applyAll();
        Mage::getModel('rackpoint/flag')->loadSelf()
            ->setState(0)
            ->save();
        return $this;
    }

    /**
     * add point line into paypal items
     *
     * @param $observer
     * @return $this
     */
    public function addPointLineIntoPaypal($observer)
    {
        $paypal = $observer->getEvent()->getPaypalCart();
        $quote  = $paypal->getSalesEntity();

        if($quote->getPointUsed() > 0) {
            $paypal->addItem(Mage::helper('rackpoint')->__('Point Used'), 1, -1.00 * $quote->getPointUsed(),
                'point'
            );
        }

        return $this;
    }

    /**
     * Reset used point for persisted quote
     * @param $observer
     */
    public function loadCustomerQuoteBefore($observer)
    {
        $checkoutSession = $observer->getEvent()->getCheckoutSession();
        $checkoutSession->setResetUsedPoint(true);
    }

    /**
     * Reset used point for persisted quote
     * @param $observer
     */
    public function salesQuoteLoadAfter($observer)
    {
        if ($this->_getSession()->getResetUsedPoint() == true) {
            $quote = $observer->getEvent()->getQuote();
            $quote->addData(array(
                'point_used'     => 0,
                'point_received' => 0
            ));
        }
    }

    /**
     * Get checkout session model instance
     *
     * @return Mage_Checkout_Model_Session
     */
    protected function _getSession()
    {
        return Mage::getSingleton('checkout/session');
    }
}
