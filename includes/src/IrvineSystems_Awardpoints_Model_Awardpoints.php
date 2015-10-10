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

class IrvineSystems_Awardpoints_Model_Awardpoints
{
    // Common Value Constants
    const COMMON_VALUE		= 0;

    // Points Types Constants
    const TYPE_GIFT			= 1;
    const TYPE_ORDER		= 2;
    const TYPE_REFERRAL		= 3;
    const TYPE_REVIEW		= 4;
    const TYPE_REGISTRATION	= 5;
    const TYPE_NEWSLETTER	= 6;
    const TYPE_REFORDERPAR	= 7;
    const TYPE_REFORDERCHI	= 8;

    // Review Status constants
	const STATUS_APPROVED	= 1;
	const STATUS_PENDING	= 2;
    const STATUS_UNAPPROVED	= 3;

    // Points Action constants
	const ACTION_ADD		= 1;
    const ACTION_REMOVE		= 2;

    // Rules Action constants
	const POINTS_PROCESS	= 1;
    const POINTS_NOPROCESS	= 2;

    // Rule States
	const RULE_ACTIVE		= 1;
	const RULE_INACTIVE		= 0;

    // Referral States
	const HAS_ORDER			= 1;
	const WAITING_ORDER		= 2;
	const WAITING_REG		= 3;

    // Orders Status
	const IS_CANCELED		= 'canceled';
	const IS_CLOSED			= 'closed';
	const IS_COMPLETE		= 'complete';
	const IS_FRAUD			= 'fraud';
	const IS_HOLDED			= 'holded';
	const IS_PAY_REVIEW		= 'payment_review';
	const IS_PENDING		= 'pending';
	const IS_PENDING_PAY	= 'pending_payment';
	const IS_PENDING_PAYPAL	= 'pending_paypal';
	const IS_PROCESSING		= 'processing';

   /**
    * Public getter for COMMON_VALUE constant
    *
    * @return int
    */
    public function getCommonValue()
    {
        return self::COMMON_VALUE;
    }

   /**
    * Public getter for STATUS_APPROVED constant
    *
    * @return int
    */
    public function getReviewApprovedStatus()
    {
        return self::STATUS_APPROVED;
    }

   /**
    * Public getter for STATUS_PENDING constant
    *
    * @return int
    */
    public function getReviewPendingStatus()
    {
        return self::STATUS_PENDING;
    }

   /**
    * Public getter for STATUS_UNAPPROVED constant
    *
    * @return int
    */
    public function getReviewUnapprovedStatus()
    {
        return self::STATUS_UNAPPROVED;
    }

   /**
    * Public getter for WAITING_REG constant
    *
    * @return int
    */
    public function getWaitingRegistration()
    {
        return self::WAITING_REG;
    }

   /**
    * Public getter for WAITING_ORDER constant
    *
    * @return int
    */
    public function getWaitingOrder()
    {
        return self::WAITING_ORDER;
    }

   /**
    * Public getter for HAS_ORDER constant
    *
    * @return int
    */
    public function getHasOrder()
    {
        return self::HAS_ORDER;
    }

   /**
    * Public getter for POINTS_NOPROCESS constant
    *
    * @return int
    */
    public function getNoProcessType()
    {
        return self::POINTS_NOPROCESS;
    }

   /**
    * Public getter for POINTS_PROCESS constant
    *
    * @return int
    */
    public function getProcessType()
    {
        return self::POINTS_PROCESS;
    }

   /**
    * Public getter for TYPE_NEWSLETTER constant
    *
    * @return int
    */
    public function getNewsPointsType()
    {
        return self::TYPE_NEWSLETTER;
    }

   /**
    * Public getter for TYPE_GIFT constant
    *
    * @return int
    */
    public function getGiftPointsType()
    {
        return self::TYPE_GIFT;
    }

   /**
    * Public getter for TYPE_ORDER constant
    *
    * @return int
    */
    public function getOrderPointsType()
    {
        return self::TYPE_ORDER;
    }

   /**
    * Public getter for TYPE_REFERRAL constant
    *
    * @return int
    */
    public function getReferralPointsType()
    {
        return self::TYPE_REFERRAL;
    }

   /**
    * Public getter for TYPE_REVIEW constant
    *
    * @return int
    */
    public function getReviewPointsType()
    {
        return self::TYPE_REVIEW;
    }

   /**
    * Public getter for TYPE_REGISTRATION constant
    *
    * @return int
    */
    public function getRegistrationPointsType()
    {
        return self::TYPE_REGISTRATION;
    }

   /**
    * Public getter for TYPE_REFORDERPAR constant
    *
    * @return int
    */
    public function getReferralOrderParent()
    {
        return self::TYPE_REFORDERPAR;
    }

   /**
    * Public getter for TYPE_REFORDERCHI constant
    *
    * @return int
    */
    public function getReferralOrderChild()
    {
        return self::TYPE_REFORDERCHI;
    }

   /**
    * Public getter for ACTION_ADD constant
    *
    * @return int
    */
    public function getAddActionType()
    {
        return self::ACTION_ADD;
    }

   /**
    * Public getter for ACTION_REMOVE constant
    *
    * @return int
    */
    public function getRemoveActionType()
    {
        return self::ACTION_REMOVE;
    }

   /**
    * Public getter for Orders Valid Status
    * for point to be added
    * @return array
    */
    public function getOrderValidAddStatus()
    {
		return array(
			self::IS_COMPLETE,
			self::IS_PROCESSING
        );
    }

   /**
    * Public getter for Orders Valid Status
    * for point to be removed
    *
    * @return array
    */
    public function getOrderValidRemoveStatus()
    {
		return array(
			self::IS_FRAUD,
			self::IS_HOLDED,
			self::IS_COMPLETE,
			self::IS_PROCESSING,
			self::IS_PAY_REVIEW,
			self::IS_PENDING,
			self::IS_PENDING_PAY,
			self::IS_PENDING_PAYPAL
        );
    }

   /**
    * Public getter for Orders Closed Status
    *
    * @return array
    */
    public function getOrderCloseStatus()
    {
		return array(
			self::IS_CANCELED,
			self::IS_CLOSED
        );
    }

   /**
    * Public getter for Referral Status Types Array
    *
    * @return array
    */
    public function getReferralStatusTypes()
    {
        return array(
            self::HAS_ORDER			=> Mage::helper('awardpoints')->__('Has ordered'),
            self::WAITING_ORDER		=> Mage::helper('awardpoints')->__('Waiting for order'),
            self::WAITING_REG		=> Mage::helper('awardpoints')->__('Waiting for registration'),
        );
    }

   /**
    * Public getter for Actions Types Array
    *
    * @return array
    */
    public function getActionsTypes()
    {
        return array(
            self::ACTION_ADD		=> Mage::helper('awardpoints')->__('Add Points'),
            self::ACTION_REMOVE		=> Mage::helper('awardpoints')->__('Remove Points'),
        );
    }

   /**
    * Public getter for Points Types Array
    *
    * @return array
    */
    public function getPointsTypes()
    {
        return array(
            self::TYPE_GIFT			=> Mage::helper('awardpoints')->__('Store Gift'),
            self::TYPE_ORDER		=> Mage::helper('awardpoints')->__('Order'),
            self::TYPE_REFERRAL		=> Mage::helper('awardpoints')->__('Referral'),
            self::TYPE_REVIEW		=> Mage::helper('awardpoints')->__('Review'),
            self::TYPE_REGISTRATION	=> Mage::helper('awardpoints')->__('Registration'),
            self::TYPE_NEWSLETTER	=> Mage::helper('awardpoints')->__('Newsletter Subscription'),
            self::TYPE_REFORDERPAR	=> Mage::helper('awardpoints')->__('Referred Customer Order'),
            self::TYPE_REFORDERCHI	=> Mage::helper('awardpoints')->__('Referral Bonus')
        );
    }

   /**
    * Public getter for Rule States Array
    *
    * @return array
    */
    public function getRulesStates()
    {
        return array(
            self::RULE_ACTIVE		=> Mage::helper('awardpoints')->__('Active'),
            self::RULE_INACTIVE		=> Mage::helper('awardpoints')->__('Inactive'),
        );
    }

   /**
    * Public getter for Rule Actions Array
    *
    * @return array
    */
    public function getRulesActions()
    {
        return array(
            self::POINTS_PROCESS	=> Mage::helper('awardpoints')->__('Add/Remove points'),
            self::POINTS_NOPROCESS	=> Mage::helper('awardpoints')->__("Don't process points"),
        );
    }

   /**
    * Convert Rule Types to Option List
    *
    * @return array
    */
    public function rulesStatesToOptionArray()
    {
        return $this->_toOptionArray($this->getRulesStates());
    }

   /**
    * Convert Rules Actions Types to Option List
    *
    * @return array
    */
    public function ruleActionTypesToOptionArray()
    {
        return $this->_toOptionArray($this->getRulesActions());
    }

   /**
    * Convert Points Types to Option List
    *
    * @return array
    */
    public function typesToOptionArray()
    {
        return $this->_toOptionArray($this->getPointsTypes());
    }

   /**
    * Convert Actions Types to Option List
    *
    * @return array
    */
    public function actionsToOptionArray()
    {
        return $this->_toOptionArray($this->getActionsTypes());
    }

   /**
    * Convert Actions Types to Option List
    *
    * @return array
    */
    public function referralToOptionArray()
    {
        return $this->_toOptionArray($this->getReferralStatusTypes());
    }

   /**
    * Array to Option array Converter
    *
    * @return array
    */
    protected function _toOptionArray($array)
    {
        $res = array();
        foreach ($array as $value => $label) {
        	$res[] = array('value' => $value, 'label' => $label);
        }
        return $res;
    }

    /**
     * Get Store Config
     *
     * @param string $config = requested cofig
     * @return int|string
     */
    public function getConfig($config)
    {
		// Get Current Store Id
        $currStoreId = Mage::app()->getStore()->getId();
		// Set Config Array
		$cnfArr = array(
			// General Settings
			'money_points'				=> Mage::getStoreConfig('awardpoints/general/money_points',							$currStoreId),
			'points_money'				=> Mage::getStoreConfig('awardpoints/general/points_money',							$currStoreId),
			'math_method'				=> Mage::getStoreConfig('awardpoints/general/math_method',							$currStoreId),
			'cart_method'				=> Mage::getStoreConfig('awardpoints/general/cart_method',							$currStoreId),
			'points_duration'			=> Mage::getStoreConfig('awardpoints/general/points_duration',						$currStoreId),
			'auto_use'					=> Mage::getStoreConfig('awardpoints/general/auto_use',								$currStoreId),
			'step_apply'				=> Mage::getStoreConfig('awardpoints/general/step_apply',							$currStoreId),
			'step_value'				=> Mage::getStoreConfig('awardpoints/general/step_value',							$currStoreId),
			'full_use'					=> Mage::getStoreConfig('awardpoints/general/full_use',								$currStoreId),
			'coupon_codes'				=> Mage::getStoreConfig('awardpoints/general/coupon_codes',							$currStoreId),
			'store_scope'				=> Mage::getStoreConfig('awardpoints/general/store_scope',							$currStoreId),
			// Points Settings - Registration
			'registration_show'			=> Mage::getStoreConfig('awardpoints/registration/registration_show',				$currStoreId),
			'registration_points'		=> Mage::getStoreConfig('awardpoints/registration/registration_points',				$currStoreId),
			// Points Settings - Newsletter
			'newsletter_show'			=> Mage::getStoreConfig('awardpoints/newsletter/newsletter_show',					$currStoreId),
			'newsletter_points'			=> Mage::getStoreConfig('awardpoints/newsletter/newsletter_points',					$currStoreId),
			// Points Settings - Shopping
			'shopping_show'				=> Mage::getStoreConfig('awardpoints/shopping/shopping_show',						$currStoreId),
			'max_point_collect_order'	=> Mage::getStoreConfig('awardpoints/shopping/max_point_collect_order',				$currStoreId),
			'max_point_used_order'		=> Mage::getStoreConfig('awardpoints/shopping/max_point_used_order',				$currStoreId),
			'min_subtotal'				=> Mage::getStoreConfig('awardpoints/shopping/min_subtotal',						$currStoreId),
			// Points Settings - Referrals
			'referral_show'				=> Mage::getStoreConfig('awardpoints/referral/referral_show',						$currStoreId),
			'referral_points'			=> Mage::getStoreConfig('awardpoints/referral/referral_points',						$currStoreId),
			'referral_orders_mode'		=> Mage::getStoreConfig('awardpoints/referral/referral_orders_mode',				$currStoreId),
			'referral_orders_number'	=> Mage::getStoreConfig('awardpoints/referral/referral_orders_number',				$currStoreId),
			'referral_parent_order'		=> Mage::getStoreConfig('awardpoints/referral/referral_parent_order',				$currStoreId),
			'referral_child_order'		=> Mage::getStoreConfig('awardpoints/referral/referral_child_order',				$currStoreId),
			'referral_permanent'		=> Mage::getStoreConfig('awardpoints/referral/referral_permanent',					$currStoreId),
			'referral_addthis'			=> Mage::getStoreConfig('awardpoints/referral/referral_addthis',					$currStoreId),
			'referral_addthis_account'	=> Mage::getStoreConfig('awardpoints/referral/referral_addthis_account',			$currStoreId),
			// Points Settings - Reviews
			'review_show'				=> Mage::getStoreConfig('awardpoints/review/review_show',							$currStoreId),
			'review_points'				=> Mage::getStoreConfig('awardpoints/review/review_points',							$currStoreId),
			// Notifications Settings - Referral E-mail
			'referral_enable'			=> Mage::getStoreConfig('awardpoints/referralnotif/enable',							$currStoreId),
			'referral_template'			=> Mage::getStoreConfig('awardpoints/referralnotifications/template',				$currStoreId),
			// Notifications Settings - Referral Order Confirmation E-mails
			'order_enable'				=> Mage::getStoreConfig('awardpoints/referralordernotif/enable',					$currStoreId),
			'order_sender'				=> Mage::getStoreConfig('awardpoints/referralordernotif/sender',					$currStoreId),
			'order_template'			=> Mage::getStoreConfig('awardpoints/referralordernotif/template',					$currStoreId),
			// Notifications Settings - Referral Registration Confirmation E-mails
			'registration_enable'		=> Mage::getStoreConfig('awardpoints/referralregistrationnotif/enable',				$currStoreId),
			'registration_sender'		=> Mage::getStoreConfig('awardpoints/referralregistrationnotif/sender',				$currStoreId),
			'registration_template'		=> Mage::getStoreConfig('awardpoints/referralregistrationnotif/template',			$currStoreId),
			// Notifications Settings - Points Balance E-mails
			'balance_enable'			=> Mage::getStoreConfig('awardpoints/pointsbalancenotif/enable',					$currStoreId),
			'balance_sender'			=> Mage::getStoreConfig('awardpoints/pointsbalancenotif/sender',					$currStoreId),
			'balance_template'			=> Mage::getStoreConfig('awardpoints/pointsbalancenotif/template',					$currStoreId),
			'balance_frequency'			=> Mage::getStoreConfig('awardpoints/pointsbalancenotif/frequency',					$currStoreId),
			'balance_time'				=> Mage::getStoreConfig('awardpoints/pointsbalancenotif/time',						$currStoreId),
			'balance_weekday'			=> Mage::getStoreConfig('awardpoints/pointsbalancenotif/weekday',					$currStoreId),
			'balance_monthdate'			=> Mage::getStoreConfig('awardpoints/pointsbalancenotif/monthdate',					$currStoreId),
			// Notifications Settings - Points Expiration E-mails
			'expiration_enable'			=> Mage::getStoreConfig('awardpoints/pointsexpirationnotif/enable',					$currStoreId),
			'expiration_sender'			=> Mage::getStoreConfig('awardpoints/pointsexpirationnotif/sender',					$currStoreId),
			'expiration_template'		=> Mage::getStoreConfig('awardpoints/pointsexpirationnotif/template',				$currStoreId),
			'expiration_days'			=> Mage::getStoreConfig('awardpoints/pointsexpirationnotif/days',					$currStoreId),
			'expiration_frequency'		=> Mage::getStoreConfig('awardpoints/pointsexpirationnotif/frequency',				$currStoreId),
			'expiration_time'			=> Mage::getStoreConfig('awardpoints/pointsexpirationnotif/time',					$currStoreId),
			'expiration_weekday'		=> Mage::getStoreConfig('awardpoints/pointsexpirationnotif/weekday',				$currStoreId),
			'expiration_monthdate'		=> Mage::getStoreConfig('awardpoints/pointsexpirationnotif/monthdate',				$currStoreId),
		);
		return $cnfArr[$config];
	}
}