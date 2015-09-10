<?php
/*
 * Irvine Systems Shipping Japan Jp
 *
 * NOTICE OF LICENSE
 * This source file is subject to the Revised BSD License which is bundled with
 * this package in the files LICENSE.txt and LICENSE.html.
 *
 * @category	Magento Shipping Extension
 * @package		IrvineSystems_JapanPost
 * @author Irvine Systems, Inc.
 * @copyright Copyright (c) 2015, Irvine Systems, Inc. (http://irvinesystems.co.jp/)
 * @license     Revised BSD License
 */

class IrvineSystems_JapanPost_Model_Session extends Mage_Core_Model_Session_Abstract
{
	
   /**
    * Session Constructor
    *
    */
    public function __construct()
    {
        // Initialize namespace
		$namespace = 'japanpost';
		$this->init($namespace);
    }

   /**
    * Unset all session data
    *
    */
    public function unsetAll()
    {
        parent::unsetAll();
    }
}
