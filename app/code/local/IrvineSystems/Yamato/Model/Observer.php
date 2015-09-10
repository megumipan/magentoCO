<?php
/*
 * Irvine Systems Shipping Japan Ymt
 *
 * NOTICE OF LICENSE
 * This source file is subject to the Revised BSD License which is bundled with
 * this package in the files LICENSE.txt and LICENSE.html.
 *
 * @category	Magento Shipping Extension
 * @package		IrvineSystems_Yamato
 * @author Irvine Systems, Inc.
 * @copyright Copyright (c) 2015, Irvine Systems, Inc. (http://irvinesystems.co.jp/)
 * @license     Revised BSD License
 */

class IrvineSystems_Yamato_Model_Observer extends Mage_Core_Model_Abstract {
		
    // Observer Constants
    const CODE = 'yamato'; // Carrier Code
	
	// Data array definition
	protected $_slipData = array();

   /**
    * Set the customer and delivery options
	* 
    * @var $data Data to be saved
    * @var $method Shipping method
    */
	private function setSlipOptions($data,$method=null)
	{
		// Set the Delivery and Customer Option in the session		
		$model = Mage::getModel(self::CODE.'/slips');
		$model->setDeliveryOptions($data);
		$model->setCustomerOptions($data);
	}

   /**
    * Check if the Current shipping method can be processed
	* 
    * @var $shippingMethod Current Shipping Method
    */
	private function canProcess($shippingMethod)
	{
		// Split the shipping method for get the carrier code
		$code = explode("_",$shippingMethod);
		// Set default result
		$result = false;
		// If the Carrier Code do not match exit without processing
		if ($code[0] == self::CODE) $result = true;
		
		// Return the result
		return $result;
	}

	/**
    * Update Checkout Quote if the extar service were selected (Express for Mail-Bin and cool for Ta-Q-Bin)
	* 
    * @var $observer Varien_Event_Observer Event Observer Model
    */
	public function updateCheckoutQuote($observer)
	{
		// Return if the observer has already executed the request
		if (Mage::registry('yamato_reprocessing_totals')) return $this;

		// Get current controller, action and parameters
		$request	= Mage::app()->getFrontController()->getRequest();
		$controller = $request->getControllerName();
		$module		= $request->getModuleName();
		$action		= $request->getActionName();
		$params		= $request->getParams();
		
		// Check if we are in the cart page
		if ($module == 'firecheckout'){
			// if we are in the cart page reset the fees
			$this->updateFireCheckoutQuote($params);
			return;
		}
		
		// Check if we are in the cart page
		if ($module == 'checkout' && $controller == 'cart' && $action == 'index'){
			// if we are in the cart page reset the fees
			$this->clearExtraFees();
			return;
		}
		
		// If we just processed the billing or the shipping step
		if (array_key_exists('billing', $params) || array_key_exists('shipping', $params)){
			// If so reset the Extra charges if available
			$this->clearExtraFees();
			return;
		}
		
		// Execute only during Shipping step
		if (array_key_exists('shipping_method', $params)){
			// if we are in the cart page reset the fees
			$this->checkExtraFees($params);
		}
	}
	
	/**
	 * Check if any extra fee is required
	 * If soo the fee will be applied
	 *
	 * @var $params Current post parameters
	 */
	private function checkExtraFees($params)
	{
		// Instance the current quote
		$quote = Mage::getSingleton('checkout/session')->getQuote();
		// Get shipping method
		$shippingMethod = $quote->getShippingAddress()->getShippingMethod();
		// Stop processing if a shipping method is not available
		if(!$shippingMethod) return;
		// Stop processing if the current shipping method is not valid
		if(!$this->canProcess($shippingMethod)) return;
			
		// Set the Delivery and Customer Option in the session
		$this->setSlipOptions($params);

		// Instance the Shipping extension Session
		$session = Mage::getSingleton(self::CODE.'/session');
		
		// Unset the the request for charge calculation
		if ($session->getShippingNeedExpress()) $session->unsShippingNeedExpress();
		if ($session->getShippingNeedCool()) $session->unsShippingNeedCool();
		
		// Set default value
		$hasExp = 0;
		$hasCool = 0;
		// Check if the Express service was selected
		if (array_key_exists('exp_shipments_value', $params)) $hasExp = $params['exp_shipments_value'];
		// Check if the Cool service was selected
		if (array_key_exists('cool_shipments_value', $params)) $hasCool = $params['cool_shipments_value'];
		
		// If a service was selected update the session for include its charge
		if($hasExp > 0 || $hasCool > 0){
			if($hasExp > 0) $session->setShippingNeedExpress(true);
			if($hasCool > 0) $session->setShippingNeedCool(true);
			// Reprocess the Quote totals
			$this->reprocessTotals();
		}
	}
	
   /**
    * Reset any Extra Charges previously set
	* 
    */
	private function clearExtraFees()
	{
		// Instance the Shipping extension Session
		$session = Mage::getSingleton(self::CODE.'/session');
		// Chek if aditional charges are set
		if ($session->getShippingNeedExpress() || $session->getShippingNeedCool()){
			// Unset the the request for charge calculation
			if ($session->getShippingNeedExpress()) $session->unsShippingNeedExpress();
			if ($session->getShippingNeedCool()) $session->unsShippingNeedCool();
			// Reprocess the Quote totals
			$this->reprocessTotals();
		}
	}
	
	/**
	 * Check if any extra fee is required
	 * If soo the fee will be applied
	 *
	 */
	private function reprocessTotals()
	{
		// Register the "reprocessing total", this for avoid to loop it
		Mage::register('yamato_reprocessing_totals', true);

		// Instance the current quote
		$quote = Mage::getSingleton('checkout/session')->getQuote();
		// Get current shipping method
		$shippingMethod = $quote->getShippingAddress()->getShippingMethod();
		// Set the recollection of the shipping method charges and recollect Quote totals
		$quote->getShippingAddress()->setShippingMethod($shippingMethod)->setCollectShippingRates(true);
		$quote->setTotalsCollectedFlag(false)->collectTotals();
		$quote->save();

		// Once the totals has been re-processed unregister the execution from the register when completed
		Mage::unregister('yamato_reprocessing_totals');
	}
	

   /**
    * Process Order Slip Data
    *
    * @var $observer Varien_Event_Observer Event Observer Model
    */
	public function processOrderAfterSave($observer)
	{
		// Instance the Order
		$order = $observer->getEvent()->getOrder();
		
		// Get shipping method
		$shippingMethod = $order->getShippingMethod();
		// Split the shipping method for get the carrier code
		$code = explode("_",$shippingMethod);

		// Stop processing if the current shipping method is not valid
		if(!$this->canProcess($shippingMethod)) return;

		// Process the Order Slip
		$this->processOrderSlip($order, $code[1]);

		// Check if the Payment is COD, if so create the invoice for the order
		if ($this->_slipData['delivary_mode'] == 2) $this->createInvoice($order);

		// Unset all data
		Mage::getSingleton(self::CODE.'/session')->unsetAll();
	}

   /**
    * Create Order Invoice
    *
    * @param Mage_Sales_Model_Order Order Information
    */
	public function createInvoice($order)
	{
		// Check the state of the order is new otherwise do not invoice it
		if ($order->getState() == Mage_Sales_Model_Order::STATE_NEW) {
			// Check if the Order can be invoiced
			if(!$order->canInvoice()) return;
			
			// Create the invoice
			$invoice = Mage::getModel('sales/service_order', $order)->prepareInvoice();
			$invoice->setRequestedCaptureCase(Mage_Sales_Model_Order_Invoice::CAPTURE_OFFLINE);
			$invoice->register();
			$invoice->getOrder()->setCustomerNoteNotify(false);
			$invoice->getOrder()->setIsInProcess(true);
			$order->addStatusHistoryComment(Mage::helper(self::CODE)->__('Yamato Cash on Delivery Invoice.'), false);
			$transactionSave = Mage::getModel('core/resource_transaction')
				->addObject($invoice)
				->addObject($invoice->getOrder());
			$transactionSave->save();
        }
	}

   /**
    * Process Order Slip Data
    *
    * @param Mage_Sales_Model_Order Order Information
    * @param string methodCode Unique MethodCode
    * 
    */
	public function processOrderSlip($order, $methodCode)
	{
		// Get model
		$model = Mage::getModel(self::CODE.'/slips');

		// Set Customer Data
		$this->setCustomerData($order->getBillingAddress());

		// Add the Store Slip Settings to the data array
		$this->_slipData += $model->getStoreSlipSettings();

		// Set Order Data
		$this->setOrderData($order,$methodCode);

		// Set and save the data into the model
		$model->setData($this->_slipData);
		$model->save();
	}

   /**
    * Set All available Customer Information
    *
    * @param array Customer Shipping Address Information
    *
    */
	public function setCustomerData($cData)
	{
		// Set Customer Information
		$this->_slipData['customer_number']		= $cData->getCustomerId();
		$this->_slipData['customer_tel']		= $cData->getTelephone();
		$this->_slipData['customer_postcode']	= $cData->getPostcode();
		$this->_slipData['customer_full_name']	= $cData->getLastname().' ' .$cData->getFirstname();

		$AddressDett =  $cData->getStreet(); 
		$fullAddress =  $cData->getRegion() .' ' .$cData->getCity() .' ' .$AddressDett[0];
		$this->_slipData['customer_address']	= $fullAddress;
		$this->_slipData['customer_apart_name']	= $AddressDett[1];

		$customer_full_name_kana = $cData->getLastnamekana().' ' .$cData->getFirstnamekana();
		$customer_full_name_kana = mb_convert_kana( $customer_full_name_kana, 'ahnrsk',"utf-8");
		$this->_slipData['customer_full_name_kana']	= $customer_full_name_kana;
		$this->_slipData['customer_prefix']			= Mage::helper(self::CODE)->__('Sir/Lady');
	}

   /**
    * Set All Order Date Information
    *
    * @param $order Mage_Sales_Model_Order Order Information
    * @param $methodCode Unique shipping method code
    * 
    */
	public function setOrderData($order,$methodCode)
	{
		// Get Current Session data
		$session = Mage::getSingleton(self::CODE.'/session');
		/* @var $helper IrvineSystems_Sagawa_Helper_Data */
		$helper = Mage::helper(self::CODE);
		/* @var $slipModel IrvineSystems_Yamato_Model_Slips */
		$slipModel = Mage::getModel(self::CODE.'/slips');
		
		// Set Order Id
		$orderId = $order->getIncrementId();
		// Get Post Data
		$params = Mage::app()->getFrontController()->getRequest()->getParams();

		// Check if we are processing a mail-bin Shipment
		if($methodCode=='mailbin'){
			// Set default mailbin delivery mode
			$devMode = $slipModel::DEV_MODE_MAILBIN;
			// Update the delivery mode if the express service was selected
			if (Mage::getSingleton(self::CODE.'/session')->getShippingNeedExpress()) $devMode = $slipModel::DEV_MODE_MAILBIN_EXP;
			// Set the delivery mode
			$this->_slipData['delivary_mode'] = $devMode;
		}else{
			// Check if Delivery dates are available
			$this->_slipData['delivery_date'] = '';
			$this->_slipData['delivery_time'] = '';
			$this->_slipData['delivery_comment'] = '';
			if ($session->getDeliveryOptions()){
				// Set Delivery Date and Time
				$deliveryOptions = $session->getDeliveryOptions();
				$this->_slipData['delivery_date']		= isset($deliveryOptions['delivery_date'])	? $deliveryOptions['delivery_date']	: '';
				$this->_slipData['delivery_time']		= isset($deliveryOptions['delivery_time'])	? $deliveryOptions['delivery_time']	: '';
				$this->_slipData['delivery_comment']	= isset($deliveryOptions['delivery_comment'])	? $deliveryOptions['delivery_comment']	: '';
			}
	
			// Add Customer Shipping Options to the Array
			if ($session->getCustomerOptions()) $this->_slipData += $session->getCustomerOptions();
		
			// Check cash on delivery and set its information
			$devMode = $slipModel::DEV_MODE_TAQBIN;
			$codAmount = null;
			$codTaxAmount = null;
			// Check if cash on cdelivery is used and update the values
			if($_REQUEST['payment']['method'] == $helper->getCodMethod()){
				$codAmount = $helper->getCodFee();
				$codTaxAmount = $helper->getCodTax();
				$devMode = $slipModel::DEV_MODE_TAQBIN_COD;
			}
			// Set cash on delivery information
			$this->_slipData['cod_amount']		= $codAmount;
			$this->_slipData['tax_amount']		= $codTaxAmount;
			$this->_slipData['delivary_mode']	= $devMode;
			
			$customer_email_address = $order->getBillingAddress()->getEmail();
			if(isset($this->_slipData['enable_email_notice_schedule'])){
				if($this->_slipData['enable_email_notice_schedule']==1 && empty($this->_slipData['email_notice_schedule']))
					$this->_slipData['email_notice_schedule']= $customer_email_address;
			}
			if(isset($this->_slipData['enable_email_notice_complete'])){
				if($this->_slipData['enable_email_notice_complete']==1 && empty($this->_slipData['email_notice_complete']))
					$this->_slipData['email_notice_complete']= $customer_email_address;
			}
		}	
		// Get the first five items Name
		$itemsCount = 1;
		$items = $order->getItemsCollection();
		foreach($items as $item) {
			if ($itemsCount>2) break;
			$name = str_replace("\"","", $item->getName() );
			$id = $item->getSku();
			$this->_slipData['product_id_'.$itemsCount] = $id;
			$this->_slipData['product_name_'.$itemsCount] = $name;
			$itemsCount++;
		}
		
		// Set Order Id
		$this->_slipData['order_id']		= $orderId;
	}


	/* -------------------------------------------------------- */ 
	/* -------- FIRECHECKOUT COMPATIBILITY INTEGRATION -------- */ 
	/* -------------------------------------------------------- */ 

	/**
	 * Update Fire Checkout Quote if the exta service were selected (Express for Mail-Bin and cool for Ta-Q-Bin)
	 *
	 * @var $params Array request post data
	 */
	public function updateFireCheckoutQuote($params)
	{
		// Check if a shipping method is selected
		if (array_key_exists('shipping_method', $params)){
			// if it is selected check the extra fees
			$this->checkExtraFees($params);
		}else{
			// if it is not selected clear the extra fees
			$this->clearExtraFees();
		}
		
	}
}