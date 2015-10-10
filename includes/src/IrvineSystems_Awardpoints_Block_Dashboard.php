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

class IrvineSystems_Awardpoints_Block_Dashboard extends Mage_Core_Block_Template
{
    /**
     * Block Constructor
     * 
     */
    public function __construct()
    {
        // Parent Construction
		parent::__construct();
        // Set the new Template
        $this->setTemplate('awardpoints/dashboard_points.phtml');
    }

    /**
     * Getter for Customer Id
     * 
     * @return int
     */
    public function getCustomerId() {
        return Mage::getModel('customer/session')->getCustomerId();
    }

    /**
     * Getter for Store Id
     * 
     * @return int
     */
    public function getStoreId() {
        return Mage::app()->getStore()->getId();
    }

    /**
     * Getter for Customer Current Points
     * 
     * @return int
     */
    public function getPointsCurrent(){
        $model = Mage::getModel('awardpoints/account');
        return $model->getPointsCurrent($this->getCustomerId(), $this->getStoreId());
    }

    /**
     * Getter for Customer Recieve Points
     * 
     * @return int
     */
    public function getPointsReceived(){
        $model = Mage::getModel('awardpoints/account');
        return $model->getPointsReceived($this->getCustomerId(), $this->getStoreId());
    }

    /**
     * Getter for Customer Spent Points
     * 
     * @return int
     */
    public function getPointsSpent(){
        $model = Mage::getModel('awardpoints/account');
        return $model->getPointsSpent($this->getCustomerId(), $this->getStoreId());
    }

    /**
     * Getter for Customer On Hold Points
     * 
     * @return int
     */
    public function getPointsWaitingValidation(){
        $model = Mage::getModel('awardpoints/account');
        return $model->getPointsWaitingValidation($this->getCustomerId(), $this->getStoreId());
    }
}