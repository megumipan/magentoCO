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

class IrvineSystems_Awardpoints_Model_Resource_Referral extends Mage_Core_Model_Mysql4_Abstract
{
    /**
     * Resource Constructor
     * 
     */
    public function _construct()
    {
        // Initialize referral Database Table
		// @see /etc/config.xml
        $this->_init('awardpoints/referral', 'referral_id');
    }

    /**
     * Filter the database query by child E-mail
     * 
     */
    public function loadByEmail($childEmail)
    {
        // Update the Select
		$select = $this->_getReadAdapter()->select()
            ->from($this->getTable('awardpoints/referral'))
            ->where('child_email = ?',$childEmail);
        $result = $this->_getReadAdapter()->fetchRow($select);
        // Return an empty array if results are not available
        if(!$result)return array();
        // Otherwise return the results
        return $result;
    }
}