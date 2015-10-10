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

class IrvineSystems_Awardpoints_Model_Referral extends Mage_Core_Model_Abstract
{
    /**
     * Model Constructor
     * 
     */
    public function _construct()
    {
        // Construct parent
        parent::_construct();
        // Initialize referral Resource
		// @see /etc/config.xml
        $this->_init('awardpoints/referral');
    }

    /**
     * Load collection by given email
     * 
     * @param string $customerEmail Customer Email
	 * @return IrvineSystems_Awardpoints_Model_Referral
     */
    public function loadByEmail($customerEmail)
    {
		// Update collection data
        $this->addData($this->getResource()->loadByEmail($customerEmail));
        return $this;
    }

    /**
     * Do the refer
     * 
     * @param Mage_Customer_Model_Customer $parent Customer Object (Referrer)
     * @param string $email Referred Email Address
     * @param string $name Referred Full Name
     * 
     */
    public function doRefer(Mage_Customer_Model_Customer $parent, $email, $name)
    {
		// Return false if the Email is already refered
		if ($this->isReferred($email)) return false;
		
		// Set referral Data
		$this->setParentId($parent->getId())
             ->setChildEmail($email)
             ->setChildName($name)
             ->setReferralStatus(Mage::getModel('awardpoints/awardpoints')->getWaitingRegistration());
		// Return Saved Data and send email notification
        return $this->save() && $this->sendReferral($parent, $email, $name);
    }

    /**
     * Check if the Given Email is already present as referred customer
     * 
     * @param string $email Email to be checked
     * 
	 * @return bool
     */
    public function isReferred($email)
    {
		// Filter the Collection By email
        $collection = $this->getCollection()->addEmailFilter($email);
		// Return true if listing are available
        return $collection->count() ? true : false;
    }

    /**
     * Check if the order can be process for the referral
     * 
     * @param int $id Unique Customer Id
     * @param string $email Customer Email
     * 
	 * @return bool
     */
    public function checkOrderProcessing($id,$email)
    {
		// Get order Quantity Limits
		$orderMode			= Mage::getModel('awardpoints/awardpoints')->getConfig('referral_orders_mode');
		$orderNumber		= Mage::getModel('awardpoints/awardpoints')->getConfig('referral_orders_number');
		$orderValidStatus	= Mage::getModel('awardpoints/awardpoints')->getOrderValidAddStatus();
		// Collect All Customer Orders
		$customerOrders = Mage::getModel('sales/order')->getCollection()
			->addFieldToFilter('customer_id', array('eq' => array($id)))
			->addFieldToFilter('status', array('in' => $orderValidStatus));
		// Get the current order Quantity
		$validCustOrders = count($customerOrders);
		// Check if the quantity exeed the limit
		$orderValid = true;
		switch ($orderMode) {
		    case 1:
				if($validCustOrders >= $orderMode)	$orderValid = false;
				break;
		    case 3:
				if($validCustOrders > $orderNumber)	$orderValid = false;
				break;
		}
		// Return true if all checks are positive
		if($this->isReferred($email)&&$orderValid) return true;
		// Otherwise return false
		return false;
	}

    /**
     * Send email Notification for succesfull Referral
     * 
     * @param Mage_Customer_Model_Customer $parent Customer Object (Referrer)
     * @param string $childEmail Referred Email Address
     * @param string $childName Referred Full Name
     * 
     */
    public function sendReferral(Mage_Customer_Model_Customer $parent, $childEmail, $childName)
    {
		// Get trranslation Singleton
        $translate = Mage::getSingleton('core/translate');
		// Disable translation inline during Email preparation
        $translate->setTranslateInline(false);
        // Get Email Template Model
		$email = Mage::getModel('core/email_template');
        // Get config email template
		$template = Mage::getModel('awardpoints/awardpoints')->getConfig('referral_template');

		// Set sender data
        $sender  = array(
            'name'	=> $parent->getName(),
            'email'	=> $parent->getEmail()
        );

		// Set Email Body Data
        $email->setDesignConfig(array('area'=>'frontend', 'store'=>$this->getStoreId()))
                ->sendTransactional(
                    $template,
                    $sender,
                    $childEmail,
                    $childName,
                    array(
                        'referrer_name'	=> $parent->getName(),
                        'referrer_mail'	=> $parent->getEmail(),
                        'referred_name'	=> $childName,
                    )
                );

		// Re-Enable translation inline
        $translate->setTranslateInline(true);
		// Return Email suvccess message
        return $email->getSentSuccess();
    }

    /**
     * Send email Notification for Referred Order Confirmation
     * 
     * @param Mage_Customer_Model_Customer $parent Customer Object (Referrer)
     * @param Mage_Customer_Model_Customer $child Customer Object (Referred)
     * 
     */
    public function sendOrderConfirmation(Mage_Customer_Model_Customer $parent, Mage_Customer_Model_Customer $child)
    {
		// Get trranslation Singleton
        $translate = Mage::getSingleton('core/translate');
		// Disable translation inline during Email preparation
        $translate->setTranslateInline(false);
        // Get Email Template Model
        $email = Mage::getModel('core/email_template');
        // Get config email template
		$template = Mage::getModel('awardpoints/awardpoints')->getConfig('order_template');
		// Set sender data
		$sender = Mage::getModel('awardpoints/awardpoints')->getConfig('order_sender');

		// Set Email Body Data
        $email->setDesignConfig(array('area'=>'frontend', 'store'=>$this->getStoreId()))
                ->sendTransactional(
                    $template,
                    $sender,
                    $parent->getEmail(),
                    $parent->getName(),
                    array(
                        'referrer_name'	=> $parent->getName(),
                        'referred_name'	=> $child->getName(),
                        'referred_mail'	=> $child->getEmail(),
                    )
                );

		// Re-Enable translation inline
        $translate->setTranslateInline(true);
		// Return Email suvccess message
        return $email->getSentSuccess();
    }

    /**
     * Send email Notification for Referred Order Confirmation
     * 
     * @param Mage_Customer_Model_Customer $parent Customer Object (Referrer)
     * @param string $childEmail Referred Email Address
     * @param string $childName Referred Full Name
     * 
     */
    public function sendRegistrationConfirmation(Mage_Customer_Model_Customer $parent, $childEmail, $childName)
    {
		// Get trranslation Singleton
        $translate = Mage::getSingleton('core/translate');
		// Disable translation inline during Email preparation
        $translate->setTranslateInline(false);
        // Get Email Template Model
        $email = Mage::getModel('core/email_template');
        // Get config email template
		$template = Mage::getModel('awardpoints/awardpoints')->getConfig('registration_template');
		// Set sender data
		$sender = Mage::getModel('awardpoints/awardpoints')->getConfig('registration_sender');

		// Set Email Body Data
        $email->setDesignConfig(array('area'=>'frontend', 'store'=>$this->getStoreId()))
                ->sendTransactional(
                    $template,
                    $sender,
                    $parent->getEmail(),
                    $parent->getName(),
                    array(
                        'referrer_name'	=> $parent->getName(),
                        'referred_name'	=> $childName,
                        'referred_mail'	=> $childEmail,
                    )
                );

		// Re-Enable translation inline
        $translate->setTranslateInline(true);
		// Return Email suvccess message
        return $email->getSentSuccess();
    }
}