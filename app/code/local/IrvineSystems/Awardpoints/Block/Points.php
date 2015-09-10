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

class IrvineSystems_Awardpoints_Block_Points extends Mage_Core_Block_Template
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
        $this->setTemplate('referafriend/points.phtml');
		// Get the Resource Model
		$resource = Mage::getResourceModel('awardpoints/points_collection');
		// Filter the resource by Customer Id
        $points = $resource->addCustomerFilter($this->getCustomerId());
		// Return the Points Information
        $this->setPoints($points);
    }

    /**
     * Prepare Layout
     * 
     */
    public function _prepareLayout()
    {
		// Prepare parent Layout
        parent::_prepareLayout();
		// Set Pager Blacok with the points collection
        $pager = $this->getLayout()->createBlock('page/html_pager', 'awardpoints.points')
            ->setCollection($this->getPoints());
		// Set the Pager Child
        $this->setChild('pager', $pager);
		// Load the Points
        $this->getPoints()->load();
		// Return the updates layout
        return $this;
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
     * Get the Order information
     * 
     * @param $order_id (int) Unique order Id
     * 
     * @return array
     */
    public function getOrder($order_id) {
        return Mage::getModel('sales/order')->loadByIncrementId($order_id);
    }

    /**
     * Get the Pager Html
     * 
     */
    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }

    /**
     * Get the Type of Points
     * 
     * @param $pointInfo (array) Points Information
     * 
     * @return string
     */
    public function getTypeOfPoint($pointInfo)
    {
		// Get working parameters
        $helper = Mage::helper('awardpoints');
        $model = Mage::getModel('awardpoints/awardpoints');
		$pointsTypes = $model->getPointsTypes();
		$order_id = $pointInfo['order_id'];
		$points_type = $pointInfo['points_type'];
		$referral_id = $pointInfo['referral_id'];

		// Set Point Type Title
		$toHtml = '<div class="irvine-in-title"><strong>'.$pointsTypes[$points_type].'</strong></div>';

		// Switch according to the current Poiint Type and fill the additional information into the HTML
		switch ($points_type) {
			case 1: // Store Gift Type
			case 4: // Review Type
			case 5: // Registration Type
			case 6: // Newsletter Subscription Type
			case 7: // Referred Customer Order Type
			case 8: // Referral Bonus Type
	            //In these three cases it will be enough the title
        	break;
			case 2: // Order Type
	            // Add the Order Information
				$orderStatus = $this->getOrder($order_id)->getStatus();
	            $toHtml .= '<div class="irvine-in-title">'.$helper->__('Order Number: %s', $order_id).'</div>';
	            $toHtml .= '<div class="irvine-in-txt">'.$helper->__('Order Status: %s',$orderStatus).'</div>';
        	break;
			case 3: // Referral Type
				// Add referral Information
				$refData = Mage::getModel('awardpoints/referral')->load($referral_id)->getData();
				$statuses = $model->getReferralStatusTypes();
				$refFriend = $refData['child_name'];
				$refStatus = $statuses[$refData['referral_status']];
				$toHtml .= '<div class="irvine-in-title">'.$helper->__('Friend Name: %s',$refFriend).' </div>';
	            $toHtml .=  '<div class="irvine-in-txt">'.$helper->__('Referral Status: %s',$refStatus).'</div>';
        	break;
		}
		// Return the HTML string
        return $toHtml;
    }
}