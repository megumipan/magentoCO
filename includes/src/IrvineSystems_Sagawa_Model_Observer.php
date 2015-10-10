<?php
/*
 * Irvine Systems Shipping Japan Sgw
 *
 * NOTICE OF LICENSE
 * This source file is subject to the Revised BSD License which is bundled with
 * this package in the files LICENSE.txt and LICENSE.html.
 *
 * @category	Magento Shipping Extension
 * @package		IrvineSystems_Sagawa
 * @author Irvine Systems, Inc.
 * @copyright Copyright (c) 2015, Irvine Systems, Inc. (http://irvinesystems.co.jp/)
 * @license     Revised BSD License
 */

class IrvineSystems_Sagawa_Model_Observer extends Mage_Core_Model_Abstract {
		
    // Observer Constants
    const CODE = 'sagawa'; // Carrier Code

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
    * @var $method Shipping method
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
		if (Mage::registry('sagawa_reprocessing_totals')) return $this;

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
		if ($session->getShippingNeedCool()) $session->unsShippingNeedCool();

		// Set default value
		$hasCool = 0;
		// Check if the Cool service was selected
		if (array_key_exists('cool_shipments_value', $params)) $hasCool = $params['cool_shipments_value'];

		// If it was selected update the session for include its charge
		if($hasCool > 0){
			$session->setShippingNeedCool(true);
			// If a cooler service was selected update the totals
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
		if ($session->getShippingNeedCool()){
			// Unset the the request for charge calculation
			$session->unsShippingNeedCool();
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
		Mage::register('sagawa_reprocessing_totals', true);

		// Instance the current quote
		$quote = Mage::getSingleton('checkout/session')->getQuote();
		// Get current shipping method
		$shippingMethod = $quote->getShippingAddress()->getShippingMethod();
		// Set the recollection of the shipping method charges and recollect Quote totals
		$quote->getShippingAddress()->setShippingMethod($shippingMethod)->setCollectShippingRates(true);
		$quote->setTotalsCollectedFlag(false)->collectTotals();
		$quote->save();

		// Once the totals has been re-processed unregister the execution from the register when completed
		Mage::unregister('sagawa_reprocessing_totals');
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
		if ($this->_slipData['payment_source'] == 2) $this->createInvoice($order);

		// Unset all data
		Mage::getSingleton(self::CODE.'/session')->unsetAll();
	}

   /**
    * Create Order Invoice
    * 
    * @param Mage_Sales_Model_Order Order Information
    */
	private function createInvoice($order)
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
			$order->addStatusHistoryComment(Mage::helper(self::CODE)->__('Sagawa Cash on Delivery Invoice.'), false);
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
	private function processOrderSlip($order, $methodCode)
	{
		// Get model
		$model = Mage::getModel(self::CODE.'/slips');

		// Set Customer Data
		$this->setCustomerData($order->getBillingAddress());

		// Add the Store Slip Settings to the data array
		$this->_slipData += $model->getStoreSlipSettings();

		// Set Order Data
		$this->setOrderData($order, $methodCode);

		// Set Services Codes
		$this->_slipData += $model->getServicesCodes($this->_slipData);

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
	private function setCustomerData($cData)
	{
		// Set Customer Information
		$this->_slipData['customer_postcode']	= $cData->getPostcode();
		$this->_slipData['customer_name']		= $cData->getLastname().' ' .$cData->getFirstname();
		$this->_slipData['customer_namekana']	= $cData->getLastnamekana().' ' .$cData->getFirstnamekana();
		$this->_slipData['customer_address_1']	= $cData->getRegion().' ' .$cData->getCity();
		$this->_slipData['customer_address_2']	= $cData->getStreet('1');
		$this->_slipData['customer_address_3']	= $cData->getStreet('2');
		$this->_slipData['customer_tel']		= $cData->getTelephone();
		$this->_slipData['customer_email']		= $cData->getEmail();
	}

   /**
    * Set All Order Date Information
    *
    * @param Mage_Sales_Model_Order Order Information
    * @param string $methodCode Unique shipping method code
    * 
    */
	private function setOrderData($order,$methodCode)
	{
		// Get Current Session data
		$session = Mage::getSingleton(self::CODE.'/session');
		/* @var $helper IrvineSystems_Sagawa_Helper_Data */
		$helper = Mage::helper(self::CODE);
		
		// Add Customer Shipping Options to the Array  if available
		if($session->getCustomerOptions()) $this->_slipData += $session->getCustomerOptions();
		
			
		if($methodCode=='hikyakumail'){
			$this->_slipData['ship_method'] = '999';
			$paySource = Mage::getModel(self::CODE.'/slips')->getPrePaySource();
			$this->_slipData['payment_source']	= $paySource;
		}else{
			$this->_slipData['ship_method'] = '000';
			// Check if Delivery dates are available
			$this->_slipData['delivery_date'] = '';
			$this->_slipData['delivery_time'] = '';
			$this->_slipData['delivery_comment'] = '';
			if ($session->getDeliveryOptions()){
				// Set Delivery Date and Time
				$deliveryOptions = $session->getDeliveryOptions();
				$this->_slipData['delivery_date']		= isset($deliveryOptions['delivery_date'])	? $deliveryOptions['delivery_date']	: '';
				$this->_slipData['delivery_time']		= isset($deliveryOptions['delivery_time'])	? $deliveryOptions['delivery_time']	: '';
				$this->_slipData['delivery_comment']		= isset($deliveryOptions['delivery_comment'])	? $deliveryOptions['delivery_comment']	: '';
			}

			// If a delivery date was given update the delivery_mode data
			//if (isset($this->_slipData['delivery_date']) && $this->_slipData['delivery_date'] != '')
				//$this->_slipData['delivery_mode'] = Mage::getModel(self::CODE.'/slips')->getDesiredDel();
				
			//Set Payment Information
			// Set Default Values
			$codAmount = null;
			$paySource = Mage::getModel(self::CODE.'/slips')->getPrePaySource();

			// Check if the selected payment method is cash on delivery
			if($_REQUEST['payment']['method'] == $helper->getCodMethod()){
				// if so, get the Amount and update the payment source value
				$codAmount = $helper->getCodFee();
				$paySource = Mage::getModel(self::CODE.'/slips')->getCodPaySource();
			}
			// Set the Payment information into the slip
			$this->_slipData['cod_amount']		= $codAmount;
			$this->_slipData['payment_source']	= $paySource;
				
			// Get the first five items Name
			$itemsCount = 1;
			$items = $order->getItemsCollection();
			foreach($items as $item) {
				if ($itemsCount>5) break;
				$name = str_replace("\"","", $item->getName() );
				$this->_slipData['product_name_'.$itemsCount] = $name;
				$itemsCount++;
			}
		}
		
		//Set Order
		$this->_slipData['order_id']		= $order->getIncrementId();
	}

   /**
    * Convert the date into a Sagawa valid format
    *
    * @param date $date Customer selected delivery date
    * 
    * @retun string Formatted Date
    */
	private function _formatDate($date)
	{
		// Format the date as int value
		$formatedDate = date('Ymd',strtotime($date));
		// Return the formatted value
		return $formatedDate;
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