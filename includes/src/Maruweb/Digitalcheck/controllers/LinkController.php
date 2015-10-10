<?php
class Maruweb_Digitalcheck_LinkController extends Mage_Core_Controller_Front_Action
{
	protected $_request;
	protected $_response;
	protected function getPayment()
	{
		return Mage::getSingleton('digitalcheck/method_cc');
	}
	protected function _getCheckout()
	{
		return Mage::getSingleton('checkout/session');
	}
	public function cc_receiveAction()
	{
		try {
			$this->_request = $this->getRequest();
			$this->_response = $this->getResponse();
			$order = Mage::getModel('sales/order')->loadByIncrementId($this->_request->getParam('SID'));
			if (!$order->getId()) {
				Mage::getSingleton('checkout/session')->clear();
				Mage::throwException('No order for processing found');
			}
			$payment = $order->getPayment();

			if ($payment->getIsTransactionPending()) {

				$payment->setCcTransId($this->_request->getParam('SID'));
				$payment->setTransactionId($this->_request->getParam('SID'));
				$payment->setIsTransactionClosed(false);
				$payment->setIsTransactionPending(false);
				if (Mage::getStoreConfig('payment/digitalcheck_cc/payment_action') == 'authorize') {
					$order->setState(
					Mage_Sales_Model_Order::STATE_NEW,
					Mage::getStoreConfig('payment/digitalcheck_cc/order_status'),
					Mage::helper('digitalcheck')->__('autholization success.'),
					true
					);
				} else {
					$order->setState(
					Mage_Sales_Model_Order::STATE_PROCESSING,
					true,
					Mage::helper('digitalcheck')->__('autholization success.'),
					true
					);
				}
				$order->save();
			}
		} catch (Exception $e){
			Mage::logException($e);
			Mage::getSingleton('checkout/session')->clear();
			parent::_redirect('checkout/cart');
		}
		$this->_redirect('checkout/onepage/success');
		return $this;
	}
	public function cc_cancelAction()
	{
		$this->_request = $this->getRequest();
		$this->_response = $this->getResponse();

		try {
			$order = Mage::getModel('sales/order')->loadByIncrementId($this->_request->getParam('SID'));
			if (!$order->getId()) {
				Mage::throwException('No order for processing found');
			}
			$order->setStatus(Mage_Sales_Model_Order::STATE_CANCELED);
			$order->cancel()->save()->delete();
		} catch (Exception $e){
			Mage::logException($e);
			Mage::getSingleton('checkout/session')->clear();
			parent::_redirect('checkout/cart');
		}
		Mage::getSingleton('checkout/session')->clear();
		$this->_redirect('checkout/onepage/failure');
		return $this;
	}
	public function cs_receiveAction()
	{
		$this->_redirect('checkout/onepage/success');
		return $this;
	}
	public function pe_receiveAction()
	{
		$this->_redirect('checkout/onepage/success');
		return $this;
	}
}
