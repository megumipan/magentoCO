<?php
    class Maruweb_Digitalcheck_FormController extends Mage_Core_Controller_Front_Action {
        protected function getPayment()
        {
            return Mage::getSingleton('digitalcheck/method_cc');
        }
	    protected function _getCheckout()
	    {
	        return Mage::getSingleton('checkout/session');
	    }
        public function sendAction()
        {
        try {
            $session = $this->_getCheckout();

            $order = Mage::getModel('sales/order')->load($session->getLastOrderId());
            if (!$order->getId()) {
                Mage::throwException('No order for processing found');
            }
            $this->loadLayout();
            $this->renderLayout();
        } catch (Exception $e){
            Mage::logException($e);
            parent::_redirect('checkout/cart');
        }
        }

        public function receiveAction()
        {
            $request = $this->getRequest();

            if ($this->getPayment()->receive3dsecure($request)) {
                $this->_redirect('checkout/onepage/success');
            } elseif("top") {
                $this->_redirect('checkout/cart');
            } else {
                $this->_redirect('checkout/onepage/failure');
            }
            return $this;
        }

    }
