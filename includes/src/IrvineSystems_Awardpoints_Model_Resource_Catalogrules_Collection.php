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

class IrvineSystems_Awardpoints_Model_Resource_Catalogrules_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    /**
	 * Collection Constructor
	 * 
     */
    public function _construct()
    {
		// Construct parent collection
		parent::_construct();
        // Initialize catalogrules Resource
		// @see /etc/config.xml
        $this->_init('awardpoints/catalogrules');
    }

    /**
	 * Set Collection Validation Filter
	 * 
     * @param int|array $websiteIds
     * @param int|array $customerGroupId
     * @param date $now
	 * 
     * @return Mage_CatalogRule_Model_Mysql4_Rule_Collection
     */
    public function setValidationFilter($websiteId, $customerGroupId, $now=null)
    {
        // Update date if is not available
		if (is_null($now)) {
            $now = Mage::getModel('core/date')->date('Y-m-d');
        }

        // Filter website information
        $this->getSelect()->where('status=1');
        $this->getSelect()->where('find_in_set(?, website_ids)', (int)$websiteId);
        $this->getSelect()->where('find_in_set(?, customer_group_ids)', (int)$customerGroupId);

        // Filter Data and Priority information
        $this->getSelect()->where('from_date is null or from_date<=?', $now);
        $this->getSelect()->where('to_date is null or to_date>=?', $now);
        $this->getSelect()->order('sort_order');

        // Return the updated Collection
        return $this;
    }

    /**
     * Filter collection by specified website IDs
     *
     * @param int|array $websiteIds
     * @return Mage_CatalogRule_Model_Mysql4_Rule_Collection
     */
    public function addWebsiteFilter($websiteIds)
    {
        // Update websitesIds if are not available
        if (!is_array($websiteIds)) {
            $websiteIds = array($websiteIds);
        }

        // Get quote parts
        $parts = array();
        foreach ($websiteIds as $websiteId) {
            $parts[] = $this->getConnection()->quoteInto('find_in_set(?, main_table.website_ids)', $websiteId);
        }
        // If parts are available update the collection
        if ($parts) {
            $this->getSelect()->where(new Zend_Db_Expr(implode(' OR ', $parts)));
        }

        // Return the updated Collection
        return $this;
    }
}