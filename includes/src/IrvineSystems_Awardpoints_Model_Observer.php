<?php
/*
 * Irvine Systems Award Points
 *
 * NOTICE OF LICENSE
 * This source file is subject to the Revised BSD License which is bundled with
 * this package in the files LICENSE.txt and LICENSE.html.
 *
 * @category    Magento Sale Extension
 * @package        IrvineSystems_AwardPoints
 * @author Irvine Systems, Inc.
 * @copyright Copyright (c) 2015, Irvine Systems, Inc. (http://irvinesystems.co.jp/)
 * @license     Revised BSD License
 */

class IrvineSystems_Awardpoints_Model_Observer extends Mage_Core_Model_Abstract
{
    /**
     * Notify Points Balance
     * 
     * @params Varien_Event_Observer $observer
     * 
     */
	public function notifyBalance($observer)
	{
		// Get Balance Notification Status
		$isEnable = Mage::getModel('awardpoints/awardpoints')->getConfig('balance_enable');
		// Stop processing if the notification is disable
		if (!$isEnable) return;

		// get Account Collection
		$customersBalance = Mage::getModel('awardpoints/account')->getCustomersBalance();
		
        // Exit if there is no data to process
        if (!$customersBalance) return null;

        // Get config email template
		$template = Mage::getModel('awardpoints/awardpoints')->getConfig('balance_template');
		// Set sender data
		$sender = Mage::getModel('awardpoints/awardpoints')->getConfig('balance_sender');

		// Sent the Balance Email for each customer
		foreach ($customersBalance as $customerBalance) {
			$this->_sendNotification($customerBalance,$template,$sender);
		}
	}

    /**
     * Notify Points Expiration
     * 
     * @params Varien_Event_Observer $observer
     * 
     */
	public function notifyExpiration($observer)
	{
		// Get Balance Notification Status
		$isEnable = Mage::getModel('awardpoints/awardpoints')->getConfig('expiration_enable');
		// Stop processing if the notification is disable
		if (!$isEnable) return;
		
		// Get the number of days before the expiration
		$days = Mage::getModel('awardpoints/awardpoints')->getConfig('expiration_days');
		// get Account Collection
		$customersExpiration = Mage::getModel('awardpoints/account')->getCustomersExpiration($days);
		
		// Exit if there is no data to process
        if (!$customersExpiration) return null;

        // Get config email template
		$template = Mage::getModel('awardpoints/awardpoints')->getConfig('expiration_template');
		// Set sender data
		$sender = Mage::getModel('awardpoints/awardpoints')->getConfig('expiration_sender');

		// Sent the Balance Email for each customer
		foreach ($customersExpiration as $customersExpiration) {
			if ($customersExpiration['expiring_points'] > 0) $this->_sendNotification($customersExpiration,$template,$sender);
		}
	}

    /**
     * Record Points for Review Creation
     * 
     * @params Varien_Event_Observer $observer
     * 
     */
	public function recordPointsUponReview($observer)
	{
		// Get Review Points Config
		$isEnable	= Mage::getModel('awardpoints/awardpoints')->getConfig('review_show');
		$points		= Mage::getModel('awardpoints/awardpoints')->getConfig('review_points');

		// If Review points is enable and there is an amount of point process the event
		if ($isEnable && $points > 0){
			// Get the Model
			$model = Mage::getModel('awardpoints/account');
			// Record the point for the review
			$data = array(
				'customer_id'	=> Mage::getSingleton('customer/session')->getCustomer()->getEntityId(),
				'points_type'	=> Mage::getModel('awardpoints/awardpoints')->getReviewPointsType(),
				'review_id'		=> $observer->getEvent()->getDataObject()->getReviewId(),
				'points_current'=> $points
			);
			// Register the Points
			$this->recordPoints($data);
		}
	}

    /**
     * Record Points for NewsLetter Subscription
     * 
     * @params Varien_Event_Observer $observer
     * 
     */
	public function recordPointsUponNewsletterSubscribe($observer)
	{
		// Get NewsLetter Points Config
		$isEnable	= Mage::getModel('awardpoints/awardpoints')->getConfig('newsletter_show');
		$points		= Mage::getModel('awardpoints/awardpoints')->getConfig('newsletter_points');
		
		// If NewsLetter points is enable and there is an amount of point process the event
		if ($isEnable && $points > 0){

		    // Get Subscriber Instance
			$subscriber = $observer->getEvent()->getSubscriber();

		    if($subscriber->getIsStatusChanged()) {
			    if($subscriber->isSubscribed()) {
					// Get the Model
					$model = Mage::getModel('awardpoints/account');
					// Get Customer Id
					$customerId = Mage::getSingleton('customer/session')->getCustomer()->getEntityId();
					// Check if the customer already has NewsPoints
					if(!$model->hasNewsPoints($customerId)){
						// If do not, add it
						$data = array(
							'customer_id'	=> $customerId,
							'points_type'	=> Mage::getModel('awardpoints/awardpoints')->getNewsPointsType(),
							'points_current'=> $points
						);
						// Register the Points
						$this->recordPoints($data);
					}
				}else{
					// Get the Model
					$model = Mage::getModel('awardpoints/account');
					// Get Customer Id
					$id = Mage::getSingleton('customer/session')->getCustomer()->getEntityId();
					// Check if the customer already has NewsPoints
					if($model->hasNewsPoints($id)){
						// If so delete it
						$model->deleteNewsPoints($id);
					}
			    }
		    }
	    }
	}

    /**
     * Record Points for Registration
     * 
     * @params Varien_Event_Observer $observer
     * 
     */
	public function recordPointsUponRegistration($observer)
	{
		// Get Account Model
		$accModel	= Mage::getModel('awardpoints/account');
		$id			= $observer->getEvent()->getCustomer()->getId();
		// Load current Customer into the model
		$hasReg = $accModel->hasRegistrationPoints($id);
		// Exit if customer has already registration points
		if($hasReg) return;

		// Get Registration Points Config
		$isEnable	= Mage::getModel('awardpoints/awardpoints')->getConfig('registration_show');
		$points		= Mage::getModel('awardpoints/awardpoints')->getConfig('registration_points');
		
		// If registration points is enable and there is an amount of point process the event
		if ($isEnable && $points > 0){
			// Set data array to be record
			$data = array(
				'customer_id'	=> $observer->getEvent()->getCustomer()->getEntityId(),
				'points_type'	=> Mage::getModel('awardpoints/awardpoints')->getRegistrationPointsType(),
				'points_current'=> $points
			);
			// Register the Points
			$this->recordPoints($data);
		}

		// Add newsletter points if selected during registration
		$hasNews = Mage::app()->getRequest()->getPost('is_subscribed');
		if(!$hasReg && $hasNews){
			$isEnable	= Mage::getModel('awardpoints/awardpoints')->getConfig('newsletter_show');
			$points		= Mage::getModel('awardpoints/awardpoints')->getConfig('newsletter_points');
		
			// If NewsLetter points is enable and there is an amount of point process the event
			if ($isEnable && $points > 0){
				$data = array(
					'customer_id'	=> $observer->getEvent()->getCustomer()->getEntityId(),
					'points_type'	=> Mage::getModel('awardpoints/awardpoints')->getNewsPointsType(),
					'points_current'=> $points
				);
				// Register the Points
				$this->recordPoints($data);
			}
		}
			
		// Get Referral Config
		$isEnable = Mage::getModel('awardpoints/awardpoints')->getConfig('referral_show');
		
		if ($isEnable) {
			// Get Referral Model
			$model	= Mage::getModel('awardpoints/referral');
			// Get Customer Information
			$id		= $observer->getEvent()->getCustomer()->getId();
			$name	= $observer->getEvent()->getCustomer()->getName();
			$email	= $observer->getEvent()->getCustomer()->getEmail();

			// Load current Customer into the model
			$model->loadByEmail($email);
			// Get Current Referral Status
			$currentStatus	= $model->getReferralStatus();
			// Instance Referral new Status
			$newStatus		= Mage::getModel('awardpoints/awardpoints')->getWaitingOrder();
			// get the points value for the  referral
			$points			= Mage::getModel('awardpoints/awardpoints')->getConfig('referral_points');

			// Stop Processing if the referred customer is not waiting for registration
			if($currentStatus && $currentStatus != Mage::getModel('awardpoints/awardpoints')->getWaitingRegistration()) return;

			// Set Current Parent Id
			$parId = $model->getParentId();
			if(!$currentStatus){
				$parId = Mage::getSingleton('awardpoints/session')->getReferralUser();
			};

			// Register the Referral
			$model->setParentId($parId)
				->setChildEmail($email)
				->setChildId($id)
				->setChildName($name)
				->setReferralStatus($newStatus);
			$model->save();

			// if the referred client is already in referral database but havent registered before add the Registration Points for the referrer
			// Check if there are point to register/gain
			if($points > 0){
				$data = array(
					'customer_id'	=> $model->getParentId(),
					'points_type'	=> Mage::getModel('awardpoints/awardpoints')->getReferralPointsType(),
					'points_current'=> $points,
					'referral_id'	=> $model->getReferralId()
				);
				// Register the Points
				$this->recordPoints($data);
			}
			
			// 
			//$this->recordPointsUponNewsletterSubscribe();

			// Get Notification status
			$isEnable = Mage::getModel('awardpoints/awardpoints')->getConfig('registration_enable');
			// Exit If Order Notification is disable
			if (!$isEnable) return;
	
			// Get parent and child information and sent Order confermation Email
			$parent = Mage::getModel('customer/customer')->load($parId);
			// Send Registration Confirmation Notification
			$model->sendRegistrationConfirmation($parent,$email,$name);
			
		}
	}

    /**
     * Record Points Upon Order Event
     * 
     * @params Varien_Event_Observer $observer
     * 
     */
	public function recordPointsUponOrder($observer)
	{
		// Get Shopping points status
		$isEnable = Mage::getModel('awardpoints/awardpoints')->getConfig('shopping_show');
		// If shopping points are not enable stop processing
		if (!$isEnable) return;
		
		// Get All Orders to be processed
		$orders = $observer->getEvent()->getOrders();
		
		// Redirect the processing as single order if there is only one order
		if ($orders == array()){
			$order = $observer->getEvent()->getOrder();
			$quote = $observer->getEvent()->getQuote();
			$this->recordSingleOrderPoints($order,$quote);
		}else{
			$this->recordMultiOrdersPoints($orders);
		}
	}

    /**
     * Record Points for Order Event
     * 
     * @params Varien_Event_Observer $order
     * @params Varien_Event_Observer $quote
     * 
     */
	protected function recordSingleOrderPoints($order,$quote)
	{
		// Set the Current quote as the order quote
		$order->setQuote($quote);

		// Process the Order points and save the data
		$this->processOrder($order);

		// Check and Process referral order success if referral points are enable
		$refEnable = Mage::getModel('awardpoints/awardpoints')->getConfig('referral_show');
		if($refEnable) $this->processReferralOrder($order);
	}

    /**
     * Record Points for Multi Orders Event
     * 
     * @params Varien_Object $orders
     * 
     */
	protected function recordMultiOrdersPoints($orders)
	{
		// Get referral Status
		$canReferralOrder = false;

		if(Mage::getModel('awardpoints/awardpoints')->getConfig('referral_show')) $canReferralOrder = true;


		// Process each order
		foreach($orders as $order){
			// Set the quote for the current order
			$order->setQuote($this->getMultishippingQuote($order));
			// Process the Order points and save the data
			$this->processOrder($order);
			// Check and Process referral order success if referral points are enable
			if($refEnable) $this->processReferralOrder($order);
		}
	}

    /**
     * Process all points for the given order and save the data into the database
     * 
     * @params Varien Object $order Order to be processed
     */
	protected function processOrder($order)
	{
		// Get customer and Order Id
		$customerId	= $order->getCustomerId();
		$orderId	= $order->getIncrementId();
		
		//check if points are already processed
		$isProc = Mage::getModel('awardpoints/account')->isOrderProcessed($customerId, $orderId);
		// if so stop processing
		if ($isProc) return;

		// Set data array to be record
		$data = array(
			'customer_id'	=> $customerId,
			'points_type'	=> Mage::getModel('awardpoints/awardpoints')->getOrderPointsType(),
			'order_id'		=> $orderId,
		);

		// Get amount of point for the current order
		$points = Mage::helper('awardpoints/data')->getPointsOnOrder($order);
		// Check if the amount of points exeed the maximum amount
		$points = Mage::helper('awardpoints/data')->checkMaxAmounts($points);

		// Update the array with the Gained Points (if any)
		if ($points > 0) $data['points_current'] = $points;

		// Get spending points
		$points = (int) Mage::helper('awardpoints/event')->getCreditPoints();
		// Update the array with the Spending Points (if any)
		if ($points > 0) $data['points_spent'] = $points;

		// Register the Points
		$this->recordPoints($data);
	}

    /**
     * Get the correct quote for the given address from a multishipping quote
     * 
     * @params Varien Object $order Order to be processed
     * @return Mage_Sales_Model_Quote
     */
	protected function getMultishippingQuote($order)
	{
		// Get Customer Shipping Address
		$orderShipAddressId		= Mage::getModel('sales/order_address')->load($order->getShippingAddressId());
		$customerShipAddressId	= $orderShipAddressId->getCustomerAddressId();

		// Get Customer Billing Address
		$orderBillAddressId		= Mage::getModel('sales/order_address')->load($order->getBillingAddressId());
		$customerBillAddressId	= $orderBillAddressId->getCustomerAddressId();

		// Get the address collection in the quote
		$quote			= Mage::getModel('sales/quote')->load($order->getQuoteId());
		$addCollection	= $quote->getAddressesCollection();

		// Get the quote model
		$quote	= Mage::getModel('sales/quote');
		
		// Find and Set the correct addresses to the quote model
		foreach($addCollection as $address){
			// get Address Type and Id
			$addType	= $address->getAddressType();
			$addId		= $address->getCustomerAddressId();

			// Compare current address with Customer Address			
			switch ($addType) {
			    case "shipping":
			        if ($addId == $customerShipAddressId) $quote->setShippingAddress($address);
		        break;
			    case "billing":
			        if ($addId == $customerBillAddressId) $quote->setBillingAddress($address);
		        break;
			}			
		}
		
		// Return the indipendent quote
		return $quote;
	}

    /**
     * Get the correct quote for the given address from a multishipping quote
     * 
     * @params Varien Object $order Order to be processed
     * @return Mage_Sales_Model_Quote
     */
	protected function processReferralOrder($order)
	{
		// Get points values from config
		$parentPoints		= Mage::getModel('awardpoints/awardpoints')->getConfig('referral_parent_order');
		$childPoints		= Mage::getModel('awardpoints/awardpoints')->getConfig('referral_child_order');
		if($parentPoints <= 0 && $childPoints <= 0)	return;	// Exit if there are no points to process

		// Get referral model	
		$model = Mage::getModel('awardpoints/referral');

		// Get customer email
		$custEmail 	= $order->getCustomerEmail();
		$custId 	= $order->getCustomerId();
		$canProcess = $model->checkOrderProcessing($custId,$custEmail);

		if(!$canProcess)	return;	// Exit if the referral order cannot be processed

		// Get waiting for order status
		$orderStatus = Mage::getModel('awardpoints/awardpoints')->getHasOrder();

		// Update referral Status
		$model->loadByEmail($custEmail);
		$model->setData('referral_status', $orderStatus);
		$model->setData('child_id', $order->getCustomerId());
		$model->save();

		// Process Referrer Poins
		if ($parentPoints > 0){
			// Set data array to be record
			$data = array(
				'store_id'		=> $order->getStoreId(),
				'customer_id'	=> $model->getData('parent_id'),
				'points_type'	=> Mage::getModel('awardpoints/awardpoints')->getReferralOrderParent(),
				'points_current'=> $parentPoints,
				'order_id'		=> $order->getIncrementId(),
			);
			// Register the Points
			$this->recordPoints($data);
		}

		// Process Referred Poins
		if ($childPoints > 0){
			// Set data array to be record
			$data = array(
				'store_id'		=> $order->getStoreId(),
				'customer_id'	=> $model->getData('child_id'),
				'points_type'	=> Mage::getModel('awardpoints/awardpoints')->getReferralOrderChild(),
				'points_current'=> $childPoints,
				'order_id'		=> $order->getIncrementId(),
			);
			// Register the Points
			$this->recordPoints($data);
		}
		
		// Get Notification status
		$isEnable = Mage::getModel('awardpoints/awardpoints')->getConfig('order_enable');
		// Exit If Order Notification is disable
		if (!$isEnable) return;

		// Get parent and child information and sent Order confermation Email
		$parent = Mage::getModel('customer/customer')->load($model->getData('parent_id'));
		$child = Mage::getModel('customer/customer')->load($model->getData('child_id'));                                
		// Send Order Notification
		$model->sendOrderConfirmation($parent, $child);
	}

    /**
     * Record Points Data
     * 
     * @params array $data Information to be saved
     * $data array structure:
     * 'customer_id'	=> int Unique Customer ID (Mandatory)
     * 'points_type'	=> int Points Type identifier (Mandatory)
     * 'points_current'=> int|float amount of point Gained (Optional)
     * 'points_spent'	=> int|float amount of point Spent (Optional)
     * 'order_id'		=> int Unique Order ID (Optional)
     * 'referral_id'	=> int Unique referred customer ID (Optional)
     */
	protected function recordPoints($data)
	{
		// Get model
		$model = Mage::getModel('awardpoints/account');

		// Set Store Id Value
		$data['store_id']	= Mage::app()->getStore()->getId();

		// Update Points Dates
		$data['date_start']	= date('Y-m-d');
		$data['date_end']	= $this->getEndDate();

		// Set and save the data into the model
		$model->setData($data);
		$model->save();
	}

    /**
     * Get The expiration date for points according to Back-End Settings
     * 
     * @return date Formatted expiration Date
     */
	protected function getEndDate()
	{
		// Get Point Duration Setting
		$extraDays = Mage::getModel('awardpoints/awardpoints')->getConfig('points_duration');
		// If a value is available set an expiration date format and return it
		if ($extraDays){
			$date = date("Y-m-d", mktime(date("H"), date("i"), date("s"), date("n"), date("j") + $extraDays, date("Y")));
			return $date;
		}
		// Otherwise return null
		return null;
	}

    /**
     * Send Email Notification
     * 
     * @params array	$data		Email Information
     * @params string	$template	Email Template
     * @params string	$sender		Email Sender
     * 
     */
    protected function _sendNotification($data,$template,$sender)
    {
		// Get trranslation Singleton
        $translate = Mage::getSingleton('core/translate');
		// Disable translation inline during Email preparation
        $translate->setTranslateInline(false);
        // Get Email Template Model
        $email = Mage::getModel('core/email_template');
        // Get Store Name
		$data['store_name'] = Mage::getModel('core/store')->load($data['store_id'])->getWebsite()->getName();
		// Set Email Body Data
        $email->setDesignConfig(array('area'=>'frontend', 'store'=>$this->getStoreId()))
                ->sendTransactional(
                    $template,
                    $sender,
                    $data['email'],
                    $data['name'],
                    $data
                );

		// Re-Enable translation inline
        $translate->setTranslateInline(true);
		// Return Email suvccess message
        return $email->getSentSuccess();
    }
}