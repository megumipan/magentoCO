<?php

class Rack_Point_Helper_Email extends Mage_Core_Helper_Abstract
{

    protected $_customer = null;

    public function setCustomer($_customer)
    {
        if (is_numeric($_customer)) {
            $this->_customer = Mage::getModel('customer/customer')->load($_customer);
        } elseif ($_customer instanceof Mage_Customer_Model_Customer) {
            $this->_customer = $_customer;
        } else {
            Mage::throwException('Invalid customer data');
        }

        return $this;
    }

    public function getCustomer()
    {
        return $this->_customer;
    }

    public function sendNewOrderEmail(Mage_Sales_Model_Order $order)
    {
        if (!$this->_canSendEmail('order_place', $order->getStoreId())) {
            return $this;
        }

        $translate = Mage::getSingleton('core/translate');
        /* @var $translate Mage_Core_Model_Translate */
        $translate->setTranslateInline(false);

        $mailTemplate = Mage::getModel('core/email_template');
        /* @var $mailTemplate Mage_Core_Model_Email_Template */
        $this->setCustomer($order->getCustomer());
        $sendTo = array(
            array(
                'email' => $this->getCustomer()->getEmail(),
                'name' => $this->getCustomerName()
            )
        );
        
        foreach ($sendTo as $recipient) {
            $mailTemplate->setDesignConfig(array('area' => 'frontend', 'store' => $order->getStoreId()))
                    ->sendTransactional(
                        $this->_getTemplate('order_place', $order->getStoreId()), 
                        $this->_getIdenfity('order_place', $order->getStoreId()), 
                        $recipient['email'], $recipient['name'], 
                        array(
                            'order' => $order,
                            'customer' => $this->getCustomer(),
                            'balance' => $this->getCustomerPointBalance($order->getStore()->getWebsite()->getId())
                        )
                    );
        }

        $translate->setTranslateInline(true);

        return $this;
    }
    
    public function sendCancelOrderEmail(Mage_Sales_Model_Order $order)
    {
        if (!$this->_canSendEmail('order_cancel', $order->getStoreId())) {
            return $this;
        }

        $translate = Mage::getSingleton('core/translate');
        /* @var $translate Mage_Core_Model_Translate */
        $translate->setTranslateInline(false);

        $mailTemplate = Mage::getModel('core/email_template');
        /* @var $mailTemplate Mage_Core_Model_Email_Template */
        
        $this->setCustomer($order->getCustomerId());
        $sendTo = array(
            array(
                'email' => $this->getCustomer()->getEmail(),
                'name' => $this->getCustomerName()
            )
        );
        
        foreach ($sendTo as $recipient) {
            $mailTemplate->setDesignConfig(array('area' => 'frontend', 'store' => $order->getStoreId()))
                    ->sendTransactional(
                        $this->_getTemplate('order_cancel', $order->getStoreId()), 
                        $this->_getIdenfity('order_cancel', $order->getStoreId()), 
                        $recipient['email'], $recipient['name'], 
                        array(
                            'order' => $order,
                            'customer' => $this->getCustomer(),
                            'balance' => $this->getCustomerPointBalance($order->getStore()->getWebsite()->getId())
                        )
                    );
        }

        $translate->setTranslateInline(true);

        return $this;
    }
    
    public function sendNewInvoiceEmail(Mage_Sales_Model_Order_Invoice $invoice)
    {
        if (!$this->_canSendEmail('invoice_created', $invoice->getOrder()->getStoreId())) {
            return $this;
        }

        $translate = Mage::getSingleton('core/translate');
        /* @var $translate Mage_Core_Model_Translate */
        $translate->setTranslateInline(false);

        $mailTemplate = Mage::getModel('core/email_template');
        $order = $invoice->getOrder();
        
        /* @var $mailTemplate Mage_Core_Model_Email_Template */
        $this->setCustomer($order->getCustomerId());
        $sendTo = array(
            array(
                'email' => $this->getCustomer()->getEmail(),
                'name' => $this->getCustomerName()
            )
        );

        foreach ($sendTo as $recipient) {
            $mailTemplate->setDesignConfig(array('area' => 'frontend', 'store' => $order->getStoreId()))
                    ->sendTransactional(
                        $this->_getTemplate('invoice_created', $order->getStoreId()), 
                        $this->_getIdenfity('invoice_created', $order->getStoreId()), 
                        $recipient['email'], $recipient['name'], 
                        array(
                            'invoice' => $invoice,
                            'order' => $order,
                            'customer' => $this->getCustomer(),
                            'balance' => $this->getCustomerPointBalance($order->getStore()->getWebsite()->getId())
                        )
                    );
        }

        $translate->setTranslateInline(true);

        return $this;
    }
    
    public function sendNewCreditmemoEmail(Mage_Sales_Model_Order_Creditmemo $creditmemo)
    {
        if (!$this->_canSendEmail('creditmemo_created', $creditmemo->getOrder()->getStoreId())) {
            return $this;
        }

        $translate = Mage::getSingleton('core/translate');
        /* @var $translate Mage_Core_Model_Translate */
        $translate->setTranslateInline(false);

        $mailTemplate = Mage::getModel('core/email_template');
        $order = $creditmemo->getOrder();
        
        /* @var $mailTemplate Mage_Core_Model_Email_Template */
        $this->setCustomer($order->getCustomerId());
        $sendTo = array(
            array(
                'email' => $this->getCustomer()->getEmail(),
                'name' => $this->getCustomerName()
            )
        );

        foreach ($sendTo as $recipient) {
            $mailTemplate->setDesignConfig(array('area' => 'frontend', 'store' => $order->getStoreId()))
                    ->sendTransactional(
                        $this->_getTemplate('creditmemo_created', $order->getStoreId()), 
                        $this->_getIdenfity('creditmemo_created', $order->getStoreId()), 
                        $recipient['email'], $recipient['name'], 
                        array(
                            'order' => $order,
                            'creditmemo' => $creditmemo,
                            'customer' => $this->getCustomer(),
                            'balance' => $this->getCustomerPointBalance($order->getStore()->getWebsite()->getId())
                        )
                    );
        }

        $translate->setTranslateInline(true);

        return $this;
    }
    
    public function sendModeratorUpdateEmail(Rack_Point_Model_Point_History $history)
    {
        if (!$this->_canSendEmail('moderator_update', 0)) {
            return $this;
        }

        $translate = Mage::getSingleton('core/translate');
        /* @var $translate Mage_Core_Model_Translate */
        $translate->setTranslateInline(false);

        $mailTemplate = Mage::getModel('core/email_template');
        
        /* @var $mailTemplate Mage_Core_Model_Email_Template */
        $this->setCustomer($history->getCustomerId());
        $sendTo = array(
            array(
                'email' => $this->getCustomer()->getEmail(),
                'name' => $this->getCustomerName()
            )
        );

        foreach ($sendTo as $recipient) {
            $mailTemplate->setDesignConfig(array('area' => 'frontend', 'store' => 0))
                    ->sendTransactional(
                        $this->_getTemplate('moderator_update', 0), 
                        $this->_getIdenfity('moderator_update', 0), 
                        $recipient['email'], $recipient['name'], 
                        array(
                            'history'  => $history,
                            'customer' => $this->getCustomer(),
                            'balance'  => $this->getCustomerPointBalance($history->getWebsiteId())
                        )
                    );
        }

        $translate->setTranslateInline(true);

        return $this;
    }
    
    public function sendExpireNotifyEmail(Rack_Point_Model_Point_Balance $balance)
    {
        if (!$this->_canSendEmail('expire_notify', $balance->getWebsiteId())) {
            return $this;
        }

        $translate = Mage::getSingleton('core/translate');
        /* @var $translate Mage_Core_Model_Translate */
        $translate->setTranslateInline(false);

        $mailTemplate = Mage::getModel('core/email_template');
        
        /* @var $mailTemplate Mage_Core_Model_Email_Template */
        $this->setCustomer($balance->getCustomerId());
        $sendTo = array(
            array(
                'email' => $this->getCustomer()->getEmail(),
                'name' => $this->getCustomerName()
            )
        );

        foreach ($sendTo as $recipient) {
            $mailTemplate->setDesignConfig(array('area' => 'frontend', 'store' => 0))
                    ->sendTransactional(
                        $this->_getTemplate('expire_notify', 0), 
                        $this->_getIdenfity('expire_notify', 0), 
                        $recipient['email'], $recipient['name'], 
                        array(
                            'customer' => $this->getCustomer(),
                            'balance'  => $balance
                        )
                    );
        }

        $translate->setTranslateInline(true);
        $balance->setLastestNotify(date('Y-m-d H:i:s'));
        $balance->setNotifyTimes($balance->getNotifyTimes() + 1);
        
        $balance->save();
        
        return $this;
    }

    public function getCustomerName()
    {
        if (!$this->_customer) {
            return false;
        }

        return $this->_customer->getLastname() . ' ' . $this->_customer->getFirstname();
    }

    public function getCustomerPointBalance($websiteId)
    {
        if (!$this->_customer) {
            return $this;
        }

        $balance = Mage::getModel('rackpoint/point_balance');
        $balance->loadByCustomerId($this->getCustomer()->getId(), $websiteId);

        return $balance;
    }

    protected function _canSendEmail($type, $storeId)
    {
        $path = 'rackpoint/' . $type . '/enable';

        return (1 == Mage::getStoreConfig($path, $storeId));
    }

    protected function _getTemplate($type, $storeId)
    {
        $path = 'rackpoint/' . $type . '/template';

        return Mage::getStoreConfig($path, $storeId);
    }

    protected function _getIdenfity($type, $storeId)
    {
        $path = 'rackpoint/' . $type . '/identity';

        return Mage::getStoreConfig($path, $storeId);
    }

}