<?php

class Rack_Point_Model_Point_Balance extends Mage_Core_Model_Abstract
{
    protected function _construct() 
    {
        $this->_init('rackpoint/point_balance');
    }
    
    public function addPointByInvoice($customerId, $invoice)
    {
        try {
            $this->getResource()->beginTransaction();
            
            $this->loadByCustomerId($customerId, $invoice->getStore()->getWebsiteId());
            if ($this->getId()) {
                $this->getResource()->addPoint($customerId, $invoice->getPointReceived(), $invoice->getStore()->getWebsiteId());
            } else {
                $current = new Zend_Date();
                $expirePeriod = Mage::helper('rackpoint')->getPointExpirePeriod();
                $this->setData(array(
                    'customer_id'   => $customerId,
                    'website_id'    => $invoice->getStore()->getWebsiteId(),
                    'balance'       => $invoice->getPointReceived(),
                    'created_at'    => $current->toString(Varien_Date::DATETIME_INTERNAL_FORMAT),
                    'expired_at'    => $current->add($expirePeriod, Zend_Date::MONTH)->toString(Varien_Date::DATETIME_INTERNAL_FORMAT)
                ));
                
                $this->save();
            }
            $history = Mage::getModel('rackpoint/point_history');
            $history->setData(array(
                'customer_id'    => $customerId,
                'website_id'     => $invoice->getStore()->getWebsiteId(),
                'point'          => $invoice->getPointReceived(),
                'action'         => Mage::helper('rackpoint')->__(Rack_Point_Model_Point_History::ACTION_BY_INVOICE),
                'comment'        => Mage::helper('rackpoint')->getCommentOfInvoiceAction($invoice->getOrder()->getIncrementId()),
                'ref_id'         => $invoice->getIncrementId(),
                'created_at'     => date('Y-m-d H:i:s'),
                'rate'           => $invoice->getOrder()->getPointRate()
            ));
            $history->save();
            $this->getResource()->commit();
            
            $mailHelper = Mage::helper('rackpoint/email');
            $mailHelper->setCustomer($customerId)->sendNewInvoiceEmail($invoice);
            
        } catch (Exception $e) {
            $this->getResource()->rollBack();
            
            throw $e;
        }
    }
    
    public function returnPointByCreditMemo($customerId, $creditMemo)
    {
        try {
            $this->getResource()->beginTransaction();
            
            //refund received point
            if ($creditMemo->getPointReceived() > 0) {
                $this->getResource()->subPoint($customerId, $creditMemo->getPointReceived(), $creditMemo->getOrder()->getStore()->getWebsiteId(), false);
                $history = Mage::getModel('rackpoint/point_history');
                $history->setData(array(
                    'customer_id'    => $customerId,
                    'website_id'     => $creditMemo->getOrder()->getStore()->getWebsiteId(),
                    'point'          => -$creditMemo->getPointReceived(),
                    'action'         => Mage::helper('rackpoint')->__(Rack_Point_Model_Point_History::ACTION_BY_RECEIVED_REFUNDED),
                    'comment'        => Mage::helper('rackpoint')->getCommentOfCreditmemoSubAction($creditMemo->getOrder()->getIncrementId()),
                    'ref_id'         => $creditMemo->getIncrementId(),
                    'created_at'     => date('Y-m-d H:i:s'),
                    'rate'           => $creditMemo->getOrder()->getPointRate()
                ));
                $history->save();
            }
            
            //refund used point            
            if ($creditMemo->getPointUsed() > 0) {
                $this->getResource()->addPoint($customerId, $creditMemo->getPointUsed(), $creditMemo->getOrder()->getStore()->getWebsiteId(), false);
                $history = Mage::getModel('rackpoint/point_history');
                $history->setData(array(
                    'customer_id'    => $customerId,
                    'website_id'     => $creditMemo->getOrder()->getStore()->getWebsiteId(),
                    'point'          => $creditMemo->getPointUsed(),
                    'action'         => Mage::helper('rackpoint')->__(Rack_Point_Model_Point_History::ACTION_BY_USED_REFUNDED),
                    'comment'        => Mage::helper('rackpoint')->getCommentOfCreditmemoAddAction($creditMemo->getOrder()->getIncrementId()),
                    'ref_id'         => $creditMemo->getIncrementId(),
                    'created_at'     => date('Y-m-d H:i:s'),
                    'rate'           => $creditMemo->getOrder()->getPointRate()
                ));
                $history->save();
            }
            $this->getResource()->commit();
            
            $mailHelper = Mage::helper('rackpoint/email');
            $mailHelper->setCustomer($customerId)->sendNewCreditmemoEmail($creditMemo);
            
        } catch (Exception $e) {
            $this->getResource()->rollBack();
            
            throw $e;
        }
    }
    
    /**
     * Substract customer balance after placed order
     * 
     * @param Mage_Sales_Model_Order $order
     * @return Rack_Point_Model_Point_Balance 
     */
    public function subPointByPlaceOrder(Mage_Sales_Model_Order $order)
    {
        if ($order->getPointUsed() > 0) {
            $this->getResource()->subPoint($order->getCustomerId(), $order->getPointUsed(), $order->getStore()->getWebsiteId(), false);
            $history = Mage::getModel('rackpoint/point_history');
            $history->setData(array(
                'customer_id'    => $order->getCustomerId(),
                'website_id'     => $order->getStore()->getWebsiteId(),
                'point'          => -$order->getPointUsed(),
                'action'         => Mage::helper('rackpoint')->__(Rack_Point_Model_Point_History::ACTION_BY_PLACE_ORDER),
                'comment'        => Mage::helper('rackpoint')->getCommentOfPlaceOrderAction($order->getIncrementId()),
                'ref_id'         => $order->getIncrementId(),
                'created_at'     => date('Y-m-d H:i:s'),
                'rate'           => $order->getPointRate()
            ));
            $history->save();

            //send order_place email here
            $mailHelper = Mage::helper('rackpoint/email');
            $mailHelper->setCustomer($order->getCustomerId())->sendNewOrderEmail($order);
        }
        
        return $this;
    }
    
    public function processPointByCancelOrder(Mage_Sales_Model_Order $order)
    {
        //cancel used points
        if ($order->getPointUsed() > 0) {
            $this->getResource()->addPoint($order->getCustomerId(), $order->getPointUsed(), $order->getStore()->getWebsiteId(), false);
            $history = Mage::getModel('rackpoint/point_history');
            $history->setData(array(
                'customer_id'    => $order->getCustomerId(),
                'website_id'     => $order->getStore()->getWebsiteId(),
                'point'          => $order->getPointUsed(),
                'action'         => Mage::helper('rackpoint')->__(Rack_Point_Model_Point_History::ACTION_BY_CANCEL_ORDER),
                'comment'        => Mage::helper('rackpoint')->getCommentOfCancelUsedAction($order->getIncrementId()),
                'ref_id'         => $order->getIncrementId(),
                'created_at'     => date('Y-m-d H:i:s'),
                'rate'           => $order->getPointRate()
            ));
            
            $history->save();
            $order->setPointUsedCanceled($order->getPointUsed());
        }
        
        //cancel received point (if need)
        if ($order->getPointReceivedInvoiced() > 0) {
            $this->getResource()->subPoint($order->getCustomerId(), $order->getPointReceivedInvoiced(), $order->getStore()->getWebsiteId(), false);
            $history = Mage::getModel('rackpoint/point_history');
            $history->setData(array(
                'customer_id'    => $order->getCustomerId(),
                'website_id'     => $order->getStore()->getWebsiteId(),
                'point'          => -$order->getPointReceivedInvoiced(),
                'action'         => Mage::helper('rackpoint')->__(Rack_Point_Model_Point_History::ACTION_BY_CANCEL_ORDER),
                'comment'        => Mage::helper('rackpoint')->getCommentOfCancelUsedAction($order->getIncrementId()),
                'ref_id'         => $order->getIncrementId(),
                'created_at'     => date('Y-m-d H:i:s'),
                'rate'           => $order->getPointRate()
            ));
            
            $history->save();
            $order->setPointReceivedCanceled($order->getPointReceivedInvoiced());
        }
        
        $mailHelper = Mage::helper('rackpoint/email');
        $mailHelper->setCustomer($order->getCustomerId())->sendCancelOrderEmail($order);
        
        return $this;
    }
    
    public function updatePoint($customerId, $websiteId, $pointDelta, $comment, $action, $sentMail = true, $refId = 'none', $updateExpired = true)
    {
        if ($pointDelta == 0) {
            return $this;
        }
        $this->loadByCustomerId($customerId, $websiteId);
        
        if ($pointDelta < 0) {
            if ($this->getId()) {
                $this->getResource()->subPoint($customerId, abs($pointDelta), $websiteId, false);
            }
        } elseif ($pointDelta > 0) {
            if ($this->getId()) {
                $this->getResource()->addPoint($customerId, $pointDelta, $websiteId, $updateExpired);
            } else {
                $current = new Zend_Date();
                $expirePeriod = Mage::helper('rackpoint')->getPointExpirePeriod();
                $this->setData(array(
                    'customer_id'   => $customerId,
                    'website_id'    => $websiteId,
                    'balance'       => $pointDelta,
                    'created_at'    => $current->toString(Varien_Date::DATETIME_INTERNAL_FORMAT),
                    'expired_at'    => $current->add($expirePeriod, Zend_Date::MONTH)->toString(Varien_Date::DATETIME_INTERNAL_FORMAT)
                ));
                $this->save();
            }
        }
        $history = Mage::getModel('rackpoint/point_history');
        $history->setData(array(
            'customer_id'    => $customerId,
            'website_id'     => $websiteId,
            'point'          => $pointDelta,
            'action'         => Mage::helper('rackpoint')->__($action),
            'comment'        => (empty($comment)) ? Mage::helper('rackpoint')->getCommentOfAdminAction() : $comment,
            'created_at'     => date('Y-m-d H:i:s'),
            'ref_id'         => $refId,
            'rate'           => Mage::helper('rackpoint')->getPointRate()
        ));

        $history->save();
        if ($sentMail == true) {
            $mailHelper = Mage::helper('rackpoint/email');
            if ($action == Rack_Point_Model_Point_History::ACTION_BY_REGISTER
                        && Mage::app()->getStore()->isAdmin() == false) {
                Mage::register('register_assign_point', new Varien_Object(array(
                    'mail_helper' => $mailHelper,
                    'history'     => $history
                )));
            } else {
                $mailHelper->setCustomer($customerId)->sendModeratorUpdateEmail($history);
            }
        }
    }

    /**
     * Load point of customer by store id
     * 
     * @param int $customerId
     * @param int $storeId
     * @return Rack_Point_Model_Point_Balance 
     */
    public function loadByCustomerId($customerId = null, $websiteId = null)
    {
        if ($customerId == null) {
            $customerId = Mage::getSingleton('customer/session')->getCustomer()->getId();
        }
        if (!$customerId) {
            return $this;
        }
        $this->getResource()->loadByCustomerId($this, $customerId, $websiteId);
        
        return $this;
    }
    
    /**
     * Get current point balance of current customer
     * 
     * @param boolean $includeCurrency
     * @param int $customerId
     * @param int $storeId
     * @return Rack_Point_Model_Point_Balance|Varien_Object
     */
    public function getBalanceOfCurrentCustomer($includeCurrency = false, $customerId = null, $websiteId = null)
    {
        if ($customerId == null) {
            $customer = Mage::getSingleton('customer/session')->getCustomer();
            $customerId = $customer->getId();
        }

        $this->loadByCustomerId($customerId, $websiteId);
        
        if ($this->isExpired() == true) {
            return new Varien_Object(array(
                            'point'         => 0, 
                            'currency'      => 0,
                            'expired_point' => $this->getBalance() ? $this->getBalance() : 0,
                            'expired_at'    => Mage::app()->getLocale()->date($this->getExpiredAt(), Varien_Date::DATETIME_INTERNAL_FORMAT),
                            'is_expired'    => true
                ));
        }
        
        if ($includeCurrency == true) {
            $money = Mage::helper('rackpoint')->point2Currency($this->getBalance());
            $result = new Varien_Object();
            $result->setData(array(
                'point'         => $this->getBalance() ? $this->getBalance() : 0,
                'currency'      => $money,
                'expired_at'    => Mage::app()->getLocale()->date($this->getExpiredAt(), Varien_Date::DATETIME_INTERNAL_FORMAT),
                'is_expired'    => $this->isExpired(),
            ));
            
            return $result;
        }
        
        return $this;
    }
    
    public function notify()
    {
        if (!$this->getId()) {
            return;
        }
        
        $mailHelper = Mage::helper('rackpoint/email');
        $mailHelper->sendExpireNotifyEmail($this);
    }
    
    public function getNotifyCollection()
    {
        $now = new Zend_Date();
        $firstDays = (int)Mage::getStoreConfig('rackpoint/expire_notify/first_notify');
        $secondDays = (int)Mage::getStoreConfig('rackpoint/expire_notify/second_notify');
        
        $where = array();
        if ($firstDays > 0) {
            $where[] = "(DATE_SUB(expired_at, INTERVAL $firstDays DAY) <= '{$now->toString(Varien_Date::DATETIME_INTERNAL_FORMAT)}' AND notify_times < 1)"; 
        }
        if ($secondDays > 0) {
            $where[] = "(DATE_SUB(expired_at, INTERVAL $secondDays DAY) <= '{$now->toString(Varien_Date::DATETIME_INTERNAL_FORMAT)}' AND notify_times = 1)"; 
        }
        
        if (!empty($where)) {
            $collection = $this->getCollection();
            $collection->getSelect()->where(implode(' OR ', $where));
            
            return  $collection;
        }
        
        return false;
    }
    
    public function isExpired()
    {
        if (!$this->getId()) {
            return 1;
        }
        
        $now = new Zend_Date();
        $expiredAt = new Zend_Date($this->getExpiredAt(), Varien_Date::DATETIME_INTERNAL_FORMAT);
        if ($now->isLater($expiredAt) || $now->equals($expiredAt)) {
            return 1;
        }
        
        return 0;
    }
    
    public function renew($customerId, $websiteId, $month = 1)
    {
        $this->getResource()->renew($customerId, $websiteId, $month);
        
        return $this;
    }
    
    public function getCurrencyBalance()
    {
        if (!$this->getId()) {
            return 0;
        }
        
        return Mage::helper('rackpoint')->point2Currency($this->getBalance());
    }

    public function getExpiredAtWithLocale()
    {
        if (!$this->getExpiredAt()) {
            return;
        }

        $expiredAt = Mage::app()->getLocale()->date($this->getExpiredAt(), Varien_Date::DATETIME_INTERNAL_FORMAT);

        return $expiredAt->toString(Varien_Date::DATETIME_INTERNAL_FORMAT);
    }

    public function getUpdatedAtEx()
    {
        return $this->getUpdatedAt() ? $this->getUpdatedAt() : $this->getCreatedAt();
    }
}