<?php
/*
 * Irvine Systems Award Points
 *
 * NOTICE OF LICENSE
 * This source file is subject to the Revised BSD License which is bundled with
 * this package in the files LICENSE.txt and LICENSE.html.
 *
 * @category    Magento Sale Extension
 * @package        IrvineSystems_AwardPoints
 * @author Irvine Systems, Inc.
 * @copyright Copyright (c) 2015, Irvine Systems, Inc. (http://irvinesystems.co.jp/)
 * @license     Revised BSD License
 */

class IrvineSystems_Awardpoints_Model_Session extends Mage_Core_Model_Session_Abstract
{
    /**
     * Model Constructor
     * 
     */
    public function __construct()
    {
		// Initialize the session
		$this->init('awardpoints');
    }

    /**
     * UnsetAll Session Information
     * 
     */
    public function unsetAll()
    {
        parent::unsetAll();
    }
}
