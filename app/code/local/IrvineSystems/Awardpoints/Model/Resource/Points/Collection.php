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

class IrvineSystems_Awardpoints_Model_Resource_Points_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    /**
     * Block Constructor
     * 
     */
    public function _construct()
    {
        // Construct parent
		parent::_construct();
        // Initialize account Resource
		// @see /etc/config.xml
        $this->_init('awardpoints/account');
    }

    /**
     * Filter Collection By Customer Id
     *
     * @param $id	(int)	Unique Customer Id
     *
	 * @return Zend_Db_Select
     */
    public function addCustomerFilter($id)
    {
		// Update Collection Select
        $this->getSelect()->where('customer_id = ?', $id);
		// Return Result
        return $this;
    }
}