<?php
class Maruweb_Digitalcheck_Model_Method_Abstract extends Mage_Payment_Model_Method_Abstract
{
	protected $_canVoid   = false;
	protected $_canSaveCc = false;
	protected function _getDcRemoteProcedureCall(&$url, $url_parameters)
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_POST,1);
		curl_setopt($ch, CURLOPT_POSTFIELDS,http_build_query($url_parameters));
		curl_setopt($ch, CURLOPT_URL,$url);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,  2);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);

		$dc_result = curl_exec($ch);
		if (curl_errno($ch)) {
			$dc_result = "failure_network\n";
		}
		curl_close($ch);
		return $dc_result;
	}

	public function getQuote() {
		return $this->getCheckout()->getQuote();
	}

	public function getCheckout() {
		if (!isset($this->_checkout)) {
			$this->_checkout = Mage::getSingleton('checkout/session');
		}
		return $this->_checkout;
	}

	public function canVoid(Varien_Object $payment)
	{
		if ($payment instanceof Mage_Sales_Model_Order_Invoice
		|| $payment instanceof Mage_Sales_Model_Order_Creditmemo
		) {
			return false;
		}

		return $this->_canVoid;
	}
}
