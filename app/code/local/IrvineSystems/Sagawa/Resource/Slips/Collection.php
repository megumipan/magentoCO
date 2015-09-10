<?php
/*
 * Irvine Systems Shipping Japan Sgw
 *
 * NOTICE OF LICENSE
 * This source file is subject to the Revised BSD License which is bundled with
 * this package in the files LICENSE.txt and LICENSE.html.
 *
 * @category	Magento Shipping Extension
 * @package		IrvineSystems_Sagawa
 * @author Irvine Systems, Inc.
 * @copyright Copyright (c) 2015, Irvine Systems, Inc. (http://irvinesystems.co.jp/)
 * @license     Revised BSD License
 */

class IrvineSystems_Sagawa_Resource_Slips_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    /**
	 * Collection Constructor
	 * 
     */
    public function _construct()
    {
        parent::_construct();
        $this->_init('sagawa/slips');
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

        // Return Collection
		return $this;
    }

    /**
     * Add Order Status filter to Collection
     *
     *
     * @param string $status Status to use for filter the order
     * @return Zend_Db_Select
     */
    public function addOrderStatusFilter($status)
    {
        // Instance the Select Object
		$select = $this->getSelect();
		// Filter on Process and complete order in the sum
		$select->where(" (order_id in  (SELECT increment_id
			FROM ".$this->getTable('sales/order')." AS orders
			WHERE orders.status IN ('".$status."'))
		) ");

        // Return Collection
        return $this;
    }

    /**
     * Add the Parcel slips type filter to Collection
     *
     * @return Zend_Db_Select
     */
    public function addParcelFilter()
    {
        // Instance the Select Object
		$select = $this->getSelect();
        // Select only Parcel type Slips
		$select->where("ship_method in ('000','001','002','003','004','005')");

        // Return Collection
        return $this;
    }

    /**
     * Add the Mail slips type filter to Collection
     *
     * @return Zend_Db_Select
     */
    public function addMailFilter()
    {
        // Instance the Select Object
		$select = $this->getSelect();
        // Select only Parcel type Slips
		$select->where("ship_method = '999'");

        // Return Collection
        return $this;
    }
}
