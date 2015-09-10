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

class IrvineSystems_Awardpoints_Model_Resource_Catalogrules extends Mage_Core_Model_Mysql4_Abstract
{
    /**
     * Resource Constructor
     * 
     */
    public function _construct()
    {    
        // Initialize catalogrules Database Table
		// @see /etc/config.xml
        $this->_init('awardpoints/catalogrules', 'rule_id');
    }
    
    /**
     * Prepare object data for saving
     * Update from and to Date information
     *
     * @param Mage_Core_Model_Abstract $object
     */
    public function _beforeSave(Mage_Core_Model_Abstract $object)
    {
		// Convert website ids and sustomer groups to strings
		$object['website_ids'] = implode(',',$object['website_ids']);
		$object['customer_group_ids'] = implode(',',$object['customer_group_ids']);

		// If the object do not have a from date set it to 0
        if (!$object->getFromDate()) {
            $date = Mage::app()->getLocale()->date();
            $date->setHour(0)
                ->setMinute(0)
                ->setSecond(0);
            $object->setFromDate($date);
        }

		// Validate from Date Format
        if ($object->getFromDate() instanceof Zend_Date) {
            $object->setFromDate($object->getFromDate()->toString(Varien_Date::DATETIME_INTERNAL_FORMAT));
        }

		// If the object do not have a to date set it null value
        if (!$object->getToDate()) {
            $object->setToDate(new Zend_Db_Expr('NULL'));
        } else {
			// Else Validate to Date Format
            if ($object->getToDate() instanceof Zend_Date) {
                $object->setToDate($object->getToDate()->toString(Varien_Date::DATETIME_INTERNAL_FORMAT));
            }
        }
		// Resume Parent Processing
		parent::_beforeSave($object);
    }
}