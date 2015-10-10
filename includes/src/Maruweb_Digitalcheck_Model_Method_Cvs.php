<?php
class Maruweb_Digitalcheck_Model_Method_Cvs extends Maruweb_Digitalcheck_Model_Method_Abstract
{
	protected $_code = 'digitalcheck_cvs';
	protected $_formBlockType = 'digitalcheck/form_cvs';
	protected $_infoBlockType = 'digitalcheck/info_cvs';

	/**
	 * Availability options
	 */
	protected $_isGateway               = true;
	protected $_canAuthorize            = false;
	protected $_canCapture              = false;
	protected $_canCapturePartial       = false;
	protected $_canRefund               = false;
	protected $_canRefundInvoicePartial = false;
	protected $_canCancel               = true;
	protected $_canUseInternal          = true;
	protected $_canUseCheckout          = true;
	protected $_canUseForMultishipping  = false;
	protected $_canFetchTransactionInfo = true;
	protected $_canReviewPayment        = true;
	protected $_isInitializeNeeded      = true;
	protected $_redirectUrl;

	protected $_checkout;
	private $_ubp3_url = 'https://www.paydesign.jp/settle/settle2/ubp3.dll';
	private $_cancvs_url = 'https://www.paydesign.jp/settle/Fixation/can_cvs.dll';

	public function assignData($data) {
		if (!($data instanceof Varien_Object)) {
			$data = new Varien_Object($data);
		}
		$info = $this->getInfoInstance();
        $info->setDcCvs($data->getDcCvs());
        $this->getCheckout()->setDcCvs($data->getDcCvs());
		$this->getCheckout()->setMethodCode($this->_code);
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
		$url_parameters['IP']  = Mage::getStoreConfig('payment/digitalcheck_cvs/ip');
		$url_parameters['SID'] = $order->getRealOrderId();
		if (Mage::getStoreConfig('payment/digitalcheck_cvs/limit_date')) {
			$url_parameters['KIGEN'] = date('Ymd', time() + Mage::getStoreConfig('payment/digitalcheck_cvs/limit_date') * 86400);
		}
		$store_number = $this->getCheckout()->getDcCvs();
		switch ($store_number) {
			case "1":
			case "2":
			case "3":
			case "71":
			case "73":
				$url_parameters['STORE'] = $store_number;
				break;
			default:
				Mage::throwException(Mage::helper('digitalcheck')->__('Invalid Store Number, %s', $store_number));
		}
		$items = $order->getAllItems();
		if (isset($items[0])) {
			$url_parameters["N1"] = mb_convert_encoding(mb_strimwidth($this->_convert_h2z($items[0]->getName()), 0, 44, '...', 'UTF-8'), 'SJIS-win', 'UTF-8');
		}
		$url_parameters["K1"] = (string)$order->getTotalDue();
		$tel = mb_ereg_replace("[^0-9]", "", mb_convert_kana($this->getQuote()->getBillingAddress()->getTelephone(), "a", "UTF-8"));
		if (strlen($tel) > 11) $tel = substr($tel, 0, 11);
		if (strlen($tel) < 9) $tel = sprintf("%09d", $tel);
		$url_parameters["TEL"] = $tel;
		$url_parameters["NAME1"] = mb_convert_encoding(mb_strimwidth($this->_convert_h2z($this->getQuote()->getBillingAddress()->getLastname()), 0, 20, '', 'UTF-8'), 'SJIS-win', 'UTF-8');
		$url_parameters["NAME2"] = mb_convert_encoding(mb_strimwidth($this->_convert_h2z($this->getQuote()->getBillingAddress()->getFirstname()), 0, 20, '', 'UTF-8'), 'SJIS-win', 'UTF-8');
		$address = sprintf("%s%s%s", $this->getQuote()->getBillingAddress()->getRegion(), $this->getQuote()->getBillingAddress()->getCity(), $this->getQuote()->getBillingAddress()->getStreet(-1));
		$address = str_replace(array("\r", "\n"), "", $address);
		$address = mb_strimwidth($address, 0, 44, '', 'UTF-8');
		$url_parameters["ADR1"] = mb_convert_encoding($this->_convert_h2z($address), 'SJIS-win', 'UTF-8');
		$url_parameters["MAIL"] = substr($this->getQuote()->getCustomerEmail(), 0, 100);
		$dc_result = $this->_getDcRemoteProcedureCall($this->_ubp3_url, $url_parameters);
		$dc_result_data = explode("\r\n", $dc_result);
		if (isset($dc_result_data[0]) === false || $dc_result_data[0] !== 'OK') {
			$order->cancel()->save();
			Mage::getSingleton('checkout/session')->clear();
			Mage::throwException(Mage::helper('digitalcheck')->__('Network Error, %s', mb_convert_encoding($dc_result, 'UTF-8','SJIS-win')));
		}
		$info->setDcCvs($store_number);
		$info->setCvsReceiptNumber($dc_result_data[3]);
		$info->setPayLimitDate($dc_result_data[4]);
		$info->setCvsReceiptUrl($dc_result_data[6]);
		$this->getCheckout()->setReceiptUrl($dc_result_data[6]);
		$this->getCheckout()->setLimitDate($dc_result_data[4]);
		$payment->setTransactionId($dc_result_data[1]);
		$payment->setIsTransactionClosed(false);
		$payment->addTransaction(Mage_Sales_Model_Order_Payment_Transaction::TYPE_AUTH);
		$order->setPayLimitDate($dc_result_data[4]);
		$order->save();
		error_reporting(E_ALL|E_STRICT);
		return true;
	}

	private function _convert_h2z($text)
	{
		//$text = mb_convert_kana($text, 'ASKV', 'UTF-8');
		$text = str_replace("'", "’", $text);
		$text = str_replace('"', '”', $text);
		$text = str_replace(";", '；', $text);
		$text = str_replace("\\", '￥', $text);
		return $text;
	}

	public function cancel(Varien_Object $payment)
	{
		$order = $payment->getOrder();
		if ($order->getState() == Mage_Sales_Model_Order::STATE_NEW && $order->getStatus() == Mage_Sales_Model_Order::STATE_PENDING_PAYMENT) {
			$this->void($payment);
		} else {
			return $this;
		}
	}

	public function void(Varien_Object $payment)
	{
		$order = $payment->getOrder();
		$url_parameters = array();
		$url_parameters['IP']  = Mage::getStoreConfig('payment/digitalcheck_cvs/ip');
		$url_parameters['PASS'] = Mage::getStoreConfig('payment/digitalcheck_cvs/pass');
		$url_parameters['SID'] = $order->getRealOrderId();
		$dc_result = $this->_getDcRemoteProcedureCall($this->_cancvs_url, $url_parameters);
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

	public function capture(Varien_Object $payment, $amount)
	{
		$order = $payment->getOrder();
		if ($order->getState() == Mage_Sales_Model_Order::STATE_PROCESSING) {
			return $this;
		}
		$url_parameters = array();
		$url_parameters['IP']  = Mage::getStoreConfig('payment/digitalcheck_cvs/ip');
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
}
