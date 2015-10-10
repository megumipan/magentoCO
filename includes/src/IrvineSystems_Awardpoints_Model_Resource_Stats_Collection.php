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

class IrvineSystems_Awardpoints_Model_Resource_Stats_Collection extends Mage_Customer_Model_Entity_Customer_Collection
{
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
		
        // Get Award Points Model
		$model = Mage::getModel('awardpoints/awardpoints');
		// Set default Value
		$commonVal = $model->getCommonValue();

		// Add point sums to select
		$select
			->from(array('act' => $this->getTable('awardpoints/account')),
			array(
			new Zend_Db_Expr('SUM(act.points_current) AS tot_points'),
			new Zend_Db_Expr('SUM(act.points_spent) AS spent_points'),
			new Zend_Db_Expr('SUM(act.points_current) - SUM(act.points_spent) AS current_points'))
			)
            ->where('act.customer_id = e.entity_id');
		
		// ** Filter Valid Referral in the sum ** //
		// Set Referral Filter Parameters
		$refTable		= $this->getTable('awardpoints/referral');
		$refPointsType	= $model->getReferralPointsType();
		$refWaiting		= $model->getWaitingRegistration();
		// Update the Select Object
		$select->where(" (act.referral_id = ".$commonVal."
                  or act.referral_id in (
				  	SELECT referral_id
					FROM ".$refTable." AS refferals
					WHERE act.points_type = ".$refPointsType."
		            AND refferals.referral_status != ".$refWaiting.")
                )");

		// ** Filter Approved Reviews in the sum ** //
		// Set Review Filter Parameters
		$revTable		= $this->getTable('review/review');
		$revPointsType	= $model->getReviewPointsType();
		$revApproved	= $model->getReviewApprovedStatus();
		// Update the Select Object
		$select->where(" (act.review_id = ".$commonVal."
                  or act.review_id in (
				  	SELECT review_id
					FROM ".$revTable." AS reviews
					WHERE act.points_type = ".$revPointsType."
		            AND reviews.status_id = ".$revApproved.")
                )");

		// ** Filter Approved Orders in the sum ** //
		// Set Order Filter Parameters
		$ordTable		= $this->getTable('sales/order');
		$ordPointsType	= $model->getOrderPointsType();
		$ordParentType	= $model->getReferralOrderParent();
		$ordChildType	= $model->getReferralOrderChild();
		$orderStatuses	= implode("','",$model->getOrderValidAddStatus());
		// Update the Select Object
		$select->where(" (act.order_id = ".$commonVal."
                  or act.order_id in (
				  	SELECT increment_id
					FROM ".$ordTable." AS orders
					WHERE act.points_type IN ('".$ordPointsType."','".$ordParentType."','".$ordChildType."')
		            AND orders.status IN ('".$orderStatuses."'))
                )");

		// Group result by Customer Id
        $select->group('act.customer_id');

		// Return Select Object
        return $this;
    }

    /**
     * Select Count Query
     * Metho\d used for adjust listing count for Pagination
     *
     * @return Zend_Db_Select
     */
	public function getSelectCountSql()
    {
        // Clone the source select
		$countSelect = clone $this->getSelect();

        // TODO: I'm keeping the comments for future references
		// Remove any Filter
        //$countSelect->reset(Zend_Db_Select::ORDER);
        //$countSelect->reset(Zend_Db_Select::LIMIT_COUNT);
        //$countSelect->reset(Zend_Db_Select::LIMIT_OFFSET);
        //$countSelect->reset(Zend_Db_Select::COLUMNS);
        //$countSelect->reset(Zend_Db_Select::GROUP);
        //$countSelect->reset(Zend_Db_Select::HAVING);

        // Count only the distinct Customer ids
        //$countSelect->columns("count(DISTINCT e.entity_id)");

        // Debug Query Logs
		//Mage::log('My SELECT BEFORE ORDING: '.$this->getSelect());
		//Mage::log('My SELECT AFTER ORDING: '.$countSelect->__toString());

		// Return Select Object
        return $countSelect;
    }
}