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

$installer = $this;
/** @var $installer Mage_Core_Model_Resource_Setup */

/**
 * Start Installation Set-up
 */
$installer->startSetup();

/**
 * Create table 'awardpoints/account'
 * Main Award Point Table for Account Management
 */
// Keep Database Table Creation in MYSQL Code for Backward compatibility
$installer->run("
DROP TABLE IF EXISTS {$this->getTable('awardpoints_account')};
CREATE TABLE {$this->getTable('awardpoints_account')} (
 `account_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Account ID',
 `store_id` smallint(5) unsigned NOT NULL COMMENT 'Store Id',
 `customer_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'Customer ID',
 `points_type` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'Type of Points',
 `points_current` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'Current Points',
 `points_spent` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'Spent Points',
 `order_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'Order ID',
 `referral_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'Referral ID',
 `review_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'Review ID',
 `date_start` date DEFAULT NULL COMMENT 'Start Date',
 `date_end` date DEFAULT NULL COMMENT 'End Date',
 PRIMARY KEY (`account_id`),
 KEY `IDX_AWARDPOINTS_ACCOUNT_ORDER_ID` (`order_id`),
 KEY `IDX_AWARDPOINTS_ACCOUNT_CUSTOMER_ID` (`customer_id`))
 ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Award Point Account Table'
");

/**
 * Create table 'awardpoints/referral'
 * Award Point Table for Referral Points Management
 */
// Keep Database Table Creation in MYSQL Code for Backward compatibility
$installer->run("
DROP TABLE IF EXISTS {$this->getTable('awardpoints_referral')};
CREATE TABLE {$this->getTable('awardpoints_referral')} (
 `referral_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Referral ID',
 `parent_id` int(10) unsigned NOT NULL COMMENT 'Referral Parent ID',
 `child_id` int(10) unsigned DEFAULT NULL COMMENT 'Referral Child ID',
 `child_email` varchar(255) DEFAULT NULL COMMENT 'Referral Child Email',
 `child_name` varchar(255) DEFAULT NULL COMMENT 'Referral Child Full Name',
 `referral_status` smallint(6) DEFAULT '0' COMMENT 'Referral Status',
 PRIMARY KEY (`referral_id`),
 UNIQUE KEY `UNQ_AWARDPOINTS_REFERRAL_CHILD_ID` (`child_id`),
 UNIQUE KEY `UNQ_AWARDPOINTS_REFERRAL_CHILD_EMAIL` (`child_email`),
 KEY `IDX_AWARDPOINTS_REFERRAL_PARENT_ID` (`parent_id`))
 ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Award Point Referral Table'
");

/**
 * Create table 'awardpoints/catalogrules'
 * Award Point Table for Catalog Points Rules Management
 */
// Keep Database Table Creation in MYSQL Code for Backward compatibility
$installer->run("
DROP TABLE IF EXISTS {$this->getTable('awardpoints_catalogrules')};
CREATE TABLE {$this->getTable('awardpoints_catalogrules')} (
 `rule_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Rule ID',
 `title` varchar(255) DEFAULT NULL COMMENT 'Rule Title',
 `description` text COMMENT 'Description',
 `from_date` date DEFAULT NULL COMMENT 'From Date',
 `to_date` date DEFAULT NULL COMMENT 'To Date',
 `website_ids` text COMMENT 'Website Ids',
 `customer_group_ids` text COMMENT 'Customer Group Ids',
 `action_type` int(11) DEFAULT NULL COMMENT 'Action_type',
 `status` smallint(6) NOT NULL DEFAULT '0' COMMENT 'Rule Status',
 `conditions_serialized` mediumtext COMMENT 'Conditions Serialized',
 `points` int(11) DEFAULT NULL COMMENT 'Points',
 `sort_order` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'Sort Order',
 PRIMARY KEY (`rule_id`))
 ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Award Point Catalog Rules Table'
");

/**
 * Create table 'awardpoints/cartrules'
 * Award Point Table for Cart Points Rules Management
 */
// Keep Database Table Creation in MYSQL Code for Backward compatibility
$installer->run("
DROP TABLE IF EXISTS {$this->getTable('awardpoints_cartrules')};
CREATE TABLE {$this->getTable('awardpoints_cartrules')} (
 `rule_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Rule ID',
 `title` varchar(255) DEFAULT NULL COMMENT 'Rule Title',
 `description` text COMMENT 'Description',
 `from_date` date DEFAULT NULL COMMENT 'From Date',
 `to_date` date DEFAULT NULL COMMENT 'To Date',
 `customer_group_ids` text COMMENT 'Customer Group Ids',
 `action_type` int(11) DEFAULT NULL COMMENT 'Action_type',
 `status` smallint(6) NOT NULL DEFAULT '0' COMMENT 'Rule Status',
 `conditions_serialized` mediumtext COMMENT 'Conditions Serialized',
 `points` int(11) DEFAULT NULL COMMENT 'Points',
 `website_ids` text COMMENT 'Website Ids',
 `sort_order` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'Sort Order',
 PRIMARY KEY (`rule_id`))
 ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Award Point Cart Rules Table'
");

/**
 * End Installation Set-up
 */
$installer->endSetup();