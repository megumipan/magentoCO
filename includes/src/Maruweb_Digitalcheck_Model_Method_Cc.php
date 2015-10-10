<?php
class Maruweb_Digitalcheck_Model_Method_Cc extends Maruweb_Digitalcheck_Model_Method_Abstract
{
	protected $_code = 'digitalcheck_cc';
	protected $_infoBlockType = 'digitalcheck/info_cc';

	/**
	 * Availability options
	 */
	protected $_isGateway               = true;
	protected $_canAuthorize            = true;
	protected $_canCapture              = true;
	protected $_canCapturePartial       = true;
	protected $_canRefund               = true;
	protected $_canRefundInvoicePartial = false;
	protected $_canCancel               = true;
	protected $_canUseInternal          = true;
	protected $_canUseCheckout          = true;
	protected $_canUseForMultishipping  = false;
	protected $_canFetchTransactionInfo = true;
	protected $_canReviewPayment        = true;
	protected $_redirectUrl             = 'digitalcheck/form/send';
	protected $_isInitializeNeeded      = true;
	protected $_canVoid                 = true;

	protected $_checkout;
	private $_ubp25_url = 'https://www.paydesign.jp/settle/settle2/ubp25.dll';
	private $_crdkakutei_url = 'https://www.paydesign.jp/settle/Fixation/crDkakutei.dll';
	private $_credit2_url = 'https://www.paydesign.jp/settle/settlex/credit2.dll';
	private $_canauthp_url = 'https://www.paydesign.jp/settle/Fixation/canauthp.dll';
	private $_cantorip_url = 'https://www.paydesign.jp/settle/Fixation/cantorip.dll';

	public function __construct()
	{
		$paymode = Mage::getStoreConfig('payment/digitalcheck_cc/paymode');
		if ($paymode != "0") {
			$this->_formBlockType = 'digitalcheck/form_cc';
		}
	}

	public function assignData($data) {
		if (!($data instanceof Varien_Object)) {
			$data = new Varien_Object($data);
		}
		$info = $this->getInfoInstance();
        $info->setDcCc($data->getDcCc());
        $this->getCheckout()->setDcCc($data->getDcCc());
		$this->getCheckout()->setMethodCode($this->_code);

		return $this;
	}

	public function authorize(Varien_Object $payment, $amount)
	{
		$order = $payment->getOrder();
		$transaction = Mage::getModel('sales/order_payment_transaction')->setTxnId($dc_result_data[1]);
		$transaction
		->setOrderPaymentObject($payment)
		->setTxnType(Mage_Sales_Model_Order_Payment_Transaction::TYPE_AUTH)
		->isFailsafe(false);

		$payment->setLastTransId($dc_result_data[1])
		->setCreatedTransaction($transaction)
		->getOrder()->addRelatedObject($transaction);
		$payment->setTransactionId($order->getRealOrderId());
		$payment->setIsTransactionClosed(false);
		$payment->setIsTransactionPending(true);
		$payment->setTransactionPendingStatus($this->getConfigData('pending_credit_order_status'));
		return $this;
	}
	public function initialize($action, $stateObject)
	{
		$info = $this->getInfoInstance();
		$order = $info->getOrder();
		$payment = $order->getPayment();

		$state = Mage_Sales_Model_Order::STATE_NEW;
		$stateObject->setState($state);
		$stateObject->setStatus(Mage_Sales_Model_Order::STATE_PENDING_PAYMENT);
		$stateObject->setIsNotified(false);

		$url_parameters = array();
		$url_parameters['IP']  = Mage::getStoreConfig('payment/digitalcheck_cc/ip');
		$url_parameters['SID'] = $order->getRealOrderId();
		$url_parameters['STORE'] = "51"; // credit = 51
		$split_number = $this->getCheckout()->getDcCc();
		list($paymode, $incount) = explode("_", $split_number);
		if (is_numeric($incount)) {
			switch ($paymode) {
				case "10":
				case "61":
				case "80":
					$url_parameters['PAYMODE'] = $paymode;
					break;
			}
			$url_parameters['INCOUNT'] = $incount;
		}
		$payment_action = Mage::getStoreConfig('payment/digitalcheck_cc/payment_action');
		switch ($payment_action) {
			case 'authorize':
				$url_parameters['KAKUTEI'] = '0';
				break;
		}
		$items = $order->getAllItems();
		if (isset($items[0])) {
			$url_parameters["N1"] = mb_convert_encoding(mb_strimwidth($items[0]->getName(), 0, 44, '...', 'UTF-8'), 'SJIS-win', 'UTF-8');
		}
		$url_parameters["K1"] = (string)$order->getTotalDue();
		$tel = mb_ereg_replace("[^0-9]", "", mb_convert_kana($this->getQuote()->getBillingAddress()->getTelephone(), "a", "UTF-8"));
		if (strlen($tel) > 11) $tel = substr($tel,0,11);
		if (strlen($tel) < 9) $tel = sprintf("%09d", $tel);
		$url_parameters["TEL"] = $tel;
		$url_parameters["NAME1"] = mb_convert_encoding(mb_strimwidth($this->getQuote()->getBillingAddress()->getLastname(), 0, 20, '', 'UTF-8'), 'SJIS-win', 'UTF-8');
		$url_parameters["NAME2"] = mb_convert_encoding(mb_strimwidth($this->getQuote()->getBillingAddress()->getFirstname(), 0, 20, '', 'UTF-8'), 'SJIS-win', 'UTF-8');
		$address = sprintf("%s%s%s", $this->getQuote()->getBillingAddress()->getRegion(), $this->getQuote()->getBillingAddress()->getCity(), $this->getQuote()->getBillingAddress()->getStreet(-1));
		$address = str_replace(array("\r", "\n"), "", $address);
		$address = mb_strimwidth($address, 0, 44, '', 'UTF-8');
		$url_parameters["ADR1"] = mb_convert_encoding($this->_convert_h2z($address), 'SJIS-win', 'UTF-8');
		$url_parameters["MAIL"] = substr($this->getQuote()->getCustomerEmail(), 0, 100);
		$dc_result = $this->_getDcRemoteProcedureCall($this->_ubp25_url, $url_parameters);
		$dc_result_data = explode("\r\n", $dc_result);
		if (isset($dc_result_data[0]) === false || $dc_result_data[0] !== 'OK') {
			$order->cancel()->save();
			$this->getCheckout()->clear();
			Mage::throwException(Mage::helper('digitalcheck')->__('Network Error, %s', mb_convert_encoding($dc_result, 'UTF-8','SJIS-win')));
		}
		$this->getCheckout()->setSettleReqCrypt($dc_result_data[3]);
		$this->getCheckout()->setSettleSeq($dc_result_data[4]);
		$payment->setTransactionId($dc_result_data[1]);
		$payment->setIsTransactionClosed(false);
		$payment->addTransaction(Mage_Sales_Model_Order_Payment_Transaction::TYPE_AUTH);
		$order->save();
		error_reporting(E_ALL|E_STRICT);
		return true;
	}

	private function _convert_h2z($text)
	{
		return $text;
	}

	public function refund(Varien_Object $payment, $amount)
	{
		$order = $payment->getOrder();
		$url_parameters = array();
		$url_parameters['IP']  = Mage::getStoreConfig('payment/digitalcheck_cc/ip');
		$url_parameters['PASS'] = Mage::getStoreConfig('payment/digitalcheck_cc/pass');
		$url_parameters['SID'] = $order->getRealOrderId();
		$dc_result = $this->_getDcRemoteProcedureCall($this->_cantorip_url, $url_parameters);
		$dc_result_data = explode("\r\n", $dc_result);
		if (isset($dc_result_data[0]) === false || strpos($dc_result_data[0], 'C-CHECK:OK') === false) {
			Mage::throwException(Mage::helper('digitalcheck')->__('Network Error, %s', $dc_result));
		}
		$order->setState(
		Mage_Sales_Model_Order::STATE_CANCELED,
		true,
		Mage::helper('digitalcheck')->__('Order refund success.'),
		true
		);
		$order->save();
		return $this;
	}

	public function cancel(Varien_Object $payment)
	{
		$order = $payment->getOrder();
		if ($order->getState() == Mage_Sales_Model_Order::STATE_PROCESSING) {
			$this->refund($payment, 0);
		} elseif ($order->getState() == Mage_Sales_Model_Order::STATE_NEW && $order->getStatus() !== Mage_Sales_Model_Order::STATE_PENDING_PAYMENT) {
			$this->void($payment);
		} else {
			return $this;
		}
	}

	public function void(Varien_Object $payment)
	{
		$order = $payment->getOrder();
		$url_parameters = array();
		$url_parameters['IP']  = Mage::getStoreConfig('payment/digitalcheck_cc/ip');
		$url_parameters['PASS'] = Mage::getStoreConfig('payment/digitalcheck_cc/pass');
		$url_parameters['SID'] = $order->getRealOrderId();
		$dc_result = $this->_getDcRemoteProcedureCall($this->_canauthp_url, $url_parameters);
		$dc_result_data = explode("\r\n", $dc_result);
		if (isset($dc_result_data[0]) === false || strpos($dc_result_data[0], 'C-CHECK:OK') === false) {
			Mage::throwException(Mage::helper('digitalcheck')->__('Network Error, %s', $dc_result));
		}
		$order->setState(
		Mage_Sales_Model_Order::STATE_CANCELED,
		true,
		Mage::helper('digitalcheck')->__('Order void success.'),
		true
		);
		$order->save();
		return $this;
	}

	/**
	 * Format credit card expiration date based on month and year values
	 * Format: mmyyyy
	 *
	 * @param string|int $month
	 * @param string|int $year
	 * @return string
	 */
	protected function _getFormattedCcExpirationDate($month, $year)
	{
		return sprintf('%02d%02d', $month, $year);
	}

	public function capture(Varien_Object $payment, $amount)
	{
		$order = $payment->getOrder();
		if ($order->getState() == Mage_Sales_Model_Order::STATE_PROCESSING) {
			return $this;
		}
		$url_parameters = array();
		$url_parameters['IP']  = Mage::getStoreConfig('payment/digitalcheck_cc/ip');
		$url_parameters['SID'] = $order->getRealOrderId();
		$dc_result = $this->_getDcRemoteProcedureCall($this->_crdkakutei_url, $url_parameters);
		$dc_result_data = explode("\r\n", $dc_result);
		if (isset($dc_result_data[0]) === false || strpos($dc_result_data[0],'C-CHECK:OK') === false) {
			Mage::throwException(Mage::helper('digitalcheck')->__('Network Error, %s', $dc_result));
		}
		$order->setState(
		Mage_Sales_Model_Order::STATE_PROCESSING,
		true,
		Mage::helper('digitalcheck')->__('Order capture success.'),
		true
		);
		$order->save();

		return $this;
	}

	public function getOrderPlaceRedirectUrl() {
		return Mage::getUrl($this->_redirectUrl);
	}
}
