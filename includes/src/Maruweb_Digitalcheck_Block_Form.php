<?php
class Maruweb_Digitalcheck_Block_Form extends Mage_Payment_Block_Form {

	protected function _construct()
	{
        $paymentCode = $this->_getCheckout()
            ->getQuote()
            ->getPayment()
            ->getMethod();
		$this->setTemplate('digitalcheck/form.phtml');
		parent::_construct();
	}


	public function getMethod()
	{
		return Mage::getModel('digitalcheck/method_cc');
	}
    /**
     * Get order object
     *
     * @return Mage_Sales_Model_Order
     */
    protected function _getOrder()
    {
        if (!$this->_order) {
            $incrementId = $this->_getCheckout()->getLastRealOrderId();
            $this->_order = Mage::getModel('sales/order')
                ->loadByIncrementId($incrementId);
        }
        return $this->_order;
    }
	protected function _getCheckout()
    {
        return Mage::getSingleton('checkout/session');
    }
	protected function _getConfig()
	{
		return Mage::getSingleton('payment/config');
	}
	public function getCcMonths()
	{
		$months = $this->getData('cc_months');
		if (is_null($months)) {
			$months[0] =  $this->__('Month');
			$months = array_merge($months, $this->_getConfig()->getMonths());
			$this->setData('cc_months', $months);
		}
		return $months;
	}

	/**
	 * Retrieve credit card expire years
	 *
	 * @return array
	 */
	public function getCcYears() {
		$years = $this->getData('cc_years');
		if (is_null($years)) {
			$years = $this->_getConfig()->getYears();
			$years = array(0=>$this->__('Year'))+$years;
			$this->setData('cc_years', $years);
		}
		return $years;
	}

	/**
	 * Retrieve availables credit card types
	 *
	 * @return array
	 */
	public function getCcTypes()
	{
		$_types = Mage::getSingleton('digitalcheck/cc_source_cctype')->toOptionArray();
		foreach ($_types as $data) {
			$types[$data['value']] = $data['label'];
		}
		return $types;
	}
	public function getCcAvailableTypes()
	{
		$types = $this->getCcTypes();
		$availableTypes = $this->getMethod()->getConfigData('cctypes');
		if ($availableTypes) {
			$availableTypes = explode(',', $availableTypes);
			foreach ($types as $code=>$name) {
				if (!in_array($code, $availableTypes)) {
					unset($types[$code]);
				}
			}
		}
		return $types;
	}

	public function isDivisionView()
	{
		if ( $this->getMethod()->getConfigData('division_view') ) {
			return true;
		}
		return false;
	}
	public function getCcTimes($code)
	{
		$_times = Mage::getSingleton('digitalcheck/cc_source_ccdivisiontimes')->toOptionArray($code);
		foreach ($_times as $data) {
			$times[$data['value']] = $data['label'];
		}
		return $times;
	}
    public function hasVerification()
    {
        if ($this->getMethod()) {
            $configData = $this->getMethod()->getConfigData('useccv');
            if(is_null($configData)){
                return true;
            }
            return (bool) $configData;
        }
        return true;
    }
	public function getCcAvailableTimes($code)
	{
		$availableTypes = $this->getMethod()->getConfigData('division_cctypes');
		if ($availableTypes) {
			$availableTypes = explode(',', $availableTypes);
			if (!in_array($code, $availableTypes)) {
				$code = "NG";
			}
		}
		$times = $this->getCcTimes($code);
		return $times;
	}

	public function isTest() {
		if ( $this->getMethod()->getConfigData('transaction_mode') == 'T' ) {
			return true;
		}
		return false;
	}
	public function getTestCardnumber() {
		return $this->getMethod()->getConfigData('test_cardnumber');
	}
}
