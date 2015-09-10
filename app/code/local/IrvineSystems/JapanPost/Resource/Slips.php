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

class IrvineSystems_JapanPost_Resource_Slips extends Mage_Core_Model_Mysql4_Abstract
{
    /**
     * Resource Constructor
     * 
     * Initialize Resorce
     * @see config.xml
     */
    public function _construct()
    {    
        $this->_init('japanpost/slips', 'slip_id');
    }
}