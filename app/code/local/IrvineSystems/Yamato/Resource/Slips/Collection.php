<?php
/*
 * Irvine Systems Shipping Japan Ymt
 *
 * NOTICE OF LICENSE
 * This source file is subject to the Revised BSD License which is bundled with
 * this package in the files LICENSE.txt and LICENSE.html.
 *
 * @category	Magento Shipping Extension
 * @package		IrvineSystems_Yamato
 * @author Irvine Systems, Inc.
 * @copyright Copyright (c) 2015, Irvine Systems, Inc. (http://irvinesystems.co.jp/)
 * @license     Revised BSD License
 */

class IrvineSystems_Yamato_Resource_Slips_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    /**
	 * Collection Constructor
	 * 
     */
    public function _construct()
    {
        parent::_construct();
        $this->_init('yamato/slips');
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
}