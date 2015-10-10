<?php
/*
 * Irvine Systems Award Points
 *
 * NOTICE OF LICENSE
 * This source file is subject to the Revised BSD License which is bundled with
 * this package in the files LICENSE.txt and LICENSE.html.
 *
 * @category	Magento Sale Extension
 * @package		IrvineSystems_AwardPoints
 * @author Irvine Systems, Inc.
 * @copyright Copyright (c) 2015, Irvine Systems, Inc. (http://irvinesystems.co.jp/)
 * @license     Revised BSD License
 */

class IrvineSystems_Awardpoints_IndexController extends Mage_Core_Controller_Front_Action
{
    /**
     * FrontEnd area entry point
     * 
     */
    public function indexAction()
    {
		// Load and Render Layout
        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * Referral Event Handler
     * 
     */
    public function referralAction()
    {
		// Check if we are sending an email invitation from the account referral
		if ($this->getRequest()->isPost() && $this->getRequest()->getPost('email')) {
			
			// Set Model
			$model		= Mage::getModel('awardpoints/referral');
			
            // Set the working Session
			$session	= Mage::getSingleton('core/session');
            // Customer Instance
            $customer	= Mage::getSingleton('customer/session')->getCustomer();
			// Set friend name and email
            $email		= $this->getRequest()->getPost('email');
            $name		= $this->getRequest()->getPost('name');
			
            // Do not process the referral if the email was already refered
			if($model->isReferred($email)){
				$session->addError(Mage::helper('awardpoints')->__('This email has been already submitted.'));
			}else if($customer->getEmail() == $email){
				$session->addError(Mage::helper('awardpoints')->__('The Email entered is your email. Please insert your friend Email.'));
			}else if($model->doRefer($customer, $email, $name)){
				$session->addSuccess(Mage::helper('awardpoints')->__('This email was successfully invited.'));
			}else{
				$session->addError(Mage::helper('awardpoints')->__('There was a problem with the invitation. Please try again later.'));
			}
        }
		
		// After Invitation Event Redirect to Index Action for Layout Render
		$this->indexAction();
    }

    /**
     * Referral Event Handler
     * NOTE: Unused Event for the Moment
     */
    public function pointsAction()
    {
		// Redirect Action to Index Handler
        $this->indexAction();
    }

    /**
     * Invitation Event Handler
     * 
     * The Function will store the Referrer id On Customer Session for acreditate the referal
	 * if the customer register to the Website or does a order
     */
    public function invitationAction(){
		// Get user Id from the request Data
        $userId = (int) $this->getRequest()->getParam('referrer');
		// Set referrer user id on the AwardPoints Session
        Mage::getSingleton('awardpoints/session')->setReferralUser($userId);
		// Get Main store URL
        $url = Mage::getUrl();
		// Redirect to mainstore URL
        $this->getResponse()->setRedirect($url);
    }

    /**
     * Remove Quotation Event Handler
     * 
     */
    public function removequotationAction(){
        // Reset Customer Session Products Check
		Mage::getSingleton('customer/session')->setProductChecked(0);
        // Reset customer credit point for event
        Mage::helper('awardpoints/event')->setCreditPoints(0);

        // Check if a referrer URL is set otherwise redirect to store default URL
        $refererUrl = $this->_getRefererUrl();
        if (empty($refererUrl)) {
            $refererUrl = empty($defaultUrl) ? Mage::getBaseUrl() : $defaultUrl;
        }

        // Get response data and redirect to referrer URL
        $this->getResponse()->setRedirect($refererUrl);
    }

    /**
     * Quotation Event Handler
     * 
     */
    public function quotationAction(){
		// Core Session Instance
        $session = Mage::getSingleton('core/session');
		// Get the Points Value to be use in the quotation
        $pointsValue	= $this->getRequest()->getPost('points_to_be_used');
		$maxPoints		= (int)Mage::getModel('awardpoints/awardpoints')->getConfig('max_point_used_order');
		// Check if a MaxPoint limiter is set in Config
        if ($maxPoints){
			// check if the current pooint to be use are allow
            if ($maxPoints < $pointsValue){
				// If the customer is trying to use points over the maximum amutn, we inform the client
                $session->addError(Mage::helper('awardpoints')->__('You tried to use %s shopping points, but you can use a maximum of %s points per shopping cart. The amount will be adjust with the maximum amount allow.', $pointsValue, $maxPoints));
				// Set the points value to the maximum amount available
                $pointsValue = $maxPoints;
            }
        }

		// Get the current Quote Id
        $quote_id = Mage::helper('checkout/cart')->getCart()->getQuote()->getId();

        // Reset Customer Session Products Check
        Mage::getSingleton('customer/session')->setProductChecked(0);
        // Set the points amount for the credit event
        Mage::helper('awardpoints/event')->setCreditPoints($pointsValue);

        // Check if a referrer URL is set otherwise redirect to store default URL
        $refererUrl = $this->_getRefererUrl();
        if (empty($refererUrl)) {
            $refererUrl = empty($defaultUrl) ? Mage::getBaseUrl() : $defaultUrl;
        }
        // Get response data and redirect to referrer URL
        $this->getResponse()->setRedirect($refererUrl);
    }

    /**
     * Actions Pre Dispatch Handler
     * These preDispatch is needed for make sure that the action 'referral' is executed only
	 * if the customer is in is acount page and is logged in.
     */
    public function preDispatch()
    {
		// init Parent preDispatch
        parent::preDispatch();
		// Get current Action Name
        $action = $this->getRequest()->getActionName();
		// If the current Action Name is referral
        if ('referral' == $action){
			// If the current Action Name is referral
            $loginUrl = Mage::helper('customer')->getLoginUrl();
			// Authenticate the current log in URL
            if (!Mage::getSingleton('customer/session')->authenticate($this, $loginUrl)) {
				// If the authentication is not passed interrupt the dispatch
                $this->setFlag('', self::FLAG_NO_DISPATCH, true);
            }
        }
    }
}