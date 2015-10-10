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

class IrvineSystems_Awardpoints_Model_Resource_Awardpoints_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    /**
	 * Collection Constructor
	 * 
     */
    public function _construct()
    {
		// Construct parent collection
        parent::_construct();
        // Initialize account Resource
		// @see /etc/config.xml
        $this->_init('awardpoints/account');
    }
    
    /**
     * Prepare Initialization Select
     *
     * @return Zend_Db_Select
     */
    protected function _initSelect()
    {
		// Initialize partent select
        parent::_initSelect();
        $select = $this->getSelect();
        $select->join(
            array('cust' => $this->getTable('customer/entity')),
            'main_table.customer_id = cust.entity_id'
        );
		// Return Result
        return $this;
    }

    /**
     * Join all valid Points for the given customer to the collection
     *
     * @param $customer_id	(int)	Unique Customer Id
     * @param $store_id 	(int)	Unique Store Id
     * @param $spent 		(bool)	Unique Store Id
     *
	 * @return Zend_Db_Select
     */
    public function joinValidPoints($customer_id, $store_id, $spent = false)
    {
        // Get Award Points Model
		$model = Mage::getModel('awardpoints/awardpoints');
		// Set default Value
		$commonVal = $model->getCommonValue();

		// Set order statuses and db columns acording to points Type
        if ($spent){
			$orderStatuses			= $model->getOrderValidRemoveStatus();
            $cols['points_spent']	= 'SUM(main_table.points_spent) as points_value';
        } else {
			$orderStatuses			= $model->getOrderValidAddStatus();
            $cols['points_current']	= 'SUM(main_table.points_current) as points_value';
        }

        // Add points Column to select object
		$this->getSelect()->from($this->getResource()->getMainTable().' as child_table', $cols);

		// ** Filter Valid Referral in the sum ** //
		// Set Referral Filter Parameters
		$refTable		= $this->getTable('awardpoints/referral');
		$refPointsType	= $model->getReferralPointsType();
		$refWaiting		= $model->getWaitingRegistration();
		// Update the Select Object
		$this->getSelect()->where(" (main_table.referral_id = ".$commonVal."
                  or main_table.referral_id in (
				  	SELECT referral_id
					FROM ".$refTable." AS refferals
					WHERE main_table.points_type = ".$refPointsType."
		            AND refferals.referral_status != ".$refWaiting.")
                )");

		// ** Filter Approved Reviews in the sum ** //
		// Set Review Filter Parameters
		$revTable		= $this->getTable('review/review');
		$revPointsType	= $model->getReviewPointsType();
		$revApproved	= $model->getReviewApprovedStatus();
		// Update the Select Object
		$this->getSelect()->where(" (main_table.review_id = ".$commonVal."
                  or main_table.review_id in (
				  	SELECT review_id
					FROM ".$revTable." AS reviews
					WHERE main_table.points_type = ".$revPointsType."
		            AND reviews.status_id = ".$revApproved.")
                )");

		// ** Filter Approved Orders in the sum ** //
		// Set Order Filter Parameters
		$ordTable		= $this->getTable('sales/order');
		$ordPointsType	= $model->getOrderPointsType();
		$ordParentType	= $model->getReferralOrderParent();
		$ordChildType	= $model->getReferralOrderChild();
		$orderStatuses	= implode("','",$orderStatuses);
		// Update the Select Object
		$this->getSelect()->where(" (main_table.order_id = ".$commonVal."
                  or main_table.order_id in (
				  	SELECT increment_id
					FROM ".$ordTable." AS orders
					WHERE main_table.points_type IN ('".$ordPointsType."','".$ordParentType."','".$ordChildType."')
		            AND orders.status IN ('".$orderStatuses."'))
                )");

		// Filter the point for the selected Customer
        $this->getSelect()->where('main_table.customer_id = ?', $customer_id)
        ->where('main_table.account_id = child_table.account_id');

		// Check if there is the need to filter store id
        if (Mage::getStoreConfig('awardpoints/general/store_scope', Mage::app()->getStore()->getId()) == 1){
			// Filter Valid Store Id
            $this->getSelect()->where('find_in_set(?, main_table.store_id)', $store_id);
        }

		// Check if there is the need to filter expiration dates
        if (Mage::getStoreConfig('awardpoints/general/points_duration', $store_id) && !$spent){
			// Filter Expired Points
            $this->getSelect()->where('( main_table.date_end >= NOW() or main_table.date_end IS NULL)');
        }

		// Group the Result by customer ID
        $this->getSelect()->group('main_table.customer_id');
		// Return Result
        return $this;
    }

    /**
     * Join all Points for the given customer to the collection
     *
     * @param $customer_id	(int)	Unique Customer Id
     * @param $store_id 	(int)	Unique Store Id
     *
	 * @return Zend_Db_Select
     */
    public function joinFullCustomerPoints($customer_id, $store_id)
	{
		// Set column point to be summed
        $cols['points_current'] = 'SUM(main_table.points_current) as points_value';

        // Add points Column to select object
        $this->getSelect()->from($this->getResource()->getMainTable().' as child_table', $cols)
                ->where('main_table.customer_id=?', $customer_id)
                ->where('main_table.account_id = child_table.account_id');

		// Check if there is the need to filter store id
        if (Mage::getStoreConfig('awardpoints/general/store_scope', $store_id) == 1){
			// Filter Valid Store Id
            $this->getSelect()->where('find_in_set(?, main_table.store_id)', $store_id);
        }

		// Check if there is the need to filter expiration dates
        if (Mage::getStoreConfig('awardpoints/general/points_duration', $store_id)){
			// Filter Expired Points
            $this->getSelect()->where('( main_table.date_end >= NOW() OR main_table.date_end IS NULL)');
        }

		// Group the Result by customer ID
        $this->getSelect()->group('main_table.customer_id');

		// Return Result
        return $this;
    }

    /**
     * Filter Collection By Expiration Days
     *
     * @param $expDate	(string)	Expiration Date
     *
	 * @return Zend_Db_Select
     */
    public function addExpDaysFilter($expDate)
    {
		// Get Today Date
		$today = date("Y-m-d"); 
		// Filter the Collection
		$this->getSelect()->where("( main_table.date_end >= '".$today."' AND main_table.date_end <= '".$expDate."')");
		// Return Result
        return $this;
    }
}