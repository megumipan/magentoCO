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

$installer = $this;
/** @var $installer Mage_Core_Model_Resource_Setup */

/**
 * Start Installation Set-up
 */
$installer->startSetup();

/**
 * Update 'awardpoints/account'
 * Update Table Structure
 * Import previous Data
 */
$installer->run("
SET FOREIGN_KEY_CHECKS=0;

ALTER TABLE {$this->getTable('sagawa_slips')}
CHANGE `delivery_date` `delivery_date` VARCHAR(15) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT 'Delivery Date';

SET FOREIGN_KEY_CHECKS=1; 
");

/**
 * End Installation Set-up
 */
$installer->endSetup();