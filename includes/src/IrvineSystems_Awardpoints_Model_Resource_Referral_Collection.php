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

class IrvineSystems_Awardpoints_Model_Resource_Referral_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    /**
	 * Collection Constructor
	 * 
     */
    public function _construct()
    {
        // Construct parent
		parent::_construct();
        // Initialize referral Resource
		// @see /etc/config.xml
        $this->_init('awardpoints/referral');
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
            'main_table.parent_id = cust.entity_id'
        );
		// Return Result
        return $this;
    }

    /**
     * Add customer Full Name to Collection
     *
     * @return Zend_Db_Select
     */
	public function addCustomerName() {
		// Get First and last name Attributes Id
		$firstnameId = Mage::getModel('eav/entity_attribute')->loadByCode('1', 'firstname')->getAttributeId();
		$lastnameId = Mage::getModel('eav/entity_attribute')->loadByCode('1', 'lastname')->getAttributeId();

		// Add customer Name and LastName to the current select and form a customer_name column
		$this->getSelect()
			->join(array('ce1' => 'customer_entity_varchar'), 'ce1.entity_id=main_table.parent_id', array('firstname' => 'value'))
			->where('ce1.attribute_id='.$firstnameId)
			->join(array('ce2' => 'customer_entity_varchar'), 'ce2.entity_id=main_table.parent_id', array('lastname' => 'value'))
			->where('ce2.attribute_id='.$lastnameId)
			->columns(new Zend_Db_Expr("CONCAT(`ce1`.`value`, ' ',`ce2`.`value`) AS customer_name"));

		// Return Result
        return $this;
    } 

    /**
     * Filter Collection By Email
     *
     * @param $email (string) Customer/Referred Email
     *
	 * @return Zend_Db_Select
     */
    public function addEmailFilter($email)
    {
		// Update Collection Select
        $this->getSelect()->where('child_email = ?', $email);
		// Return Result
        return $this;
    }

    /**
     * Filter Collection By Referral Status
     *
     * @param $email (int) Referral Status
     *
	 * @return Zend_Db_Select
     */
    public function addFlagFilter($status)
    {
		// Update Collection Select
        $this->getSelect()->where('referral_status = ?', $status);
		// Return Result
        return $this;
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
        $this->getSelect()->where('parent_id = ?', $id);
		// Return Result
        return $this;
    }
}