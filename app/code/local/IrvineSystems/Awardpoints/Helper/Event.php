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

class IrvineSystems_Awardpoints_Helper_Event extends Mage_Core_Helper_Abstract
{
    /**
     * Settter for Credit Point
     * The function will update the Customer Session with the given amount of points
	 * 
	 * @param int|float ammount of points to be set
     */
    public function setCreditPoints($points){
        Mage::getSingleton('customer/session')->setCreditPoints($points);
    }

    /**
     * Gettter for Credit Point
     * The function will return the current aount of points in Customer Session
	 * 
	 * @return int|float ammount of points in customer session
     */
    public function getCreditPoints(){
        return Mage::getSingleton('customer/session')->getCreditPoints();
    }
}