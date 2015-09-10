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
 * Update 'awardpoints/account'
 * Update Table Structure
 * Import previous Data
 */
$installer->run("
SET FOREIGN_KEY_CHECKS=0;

ALTER TABLE {$this->getTable('awardpoints_account')}
CHANGE COLUMN `awardpoints_account_id` `account_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Account ID',
CHANGE COLUMN `store_id` `store_id` smallint(5) unsigned NOT NULL COMMENT 'Store Id',
CHANGE COLUMN `customer_id` `customer_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'Customer ID',
ADD `points_type` int(10) unsigned NOT NULL DEFAULT '1' COMMENT 'Type of Points',
CHANGE COLUMN `points_current` `points_current` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'Current Points',
CHANGE COLUMN `points_spent` `points_spent` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'Spent Points',
CHANGE COLUMN `order_id` `order_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'Order ID',
CHANGE COLUMN `awardpoints_referral_id` `referral_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'Referral ID',
ADD `review_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'Review ID',
CHANGE COLUMN `date_start` `date_start` date DEFAULT NULL COMMENT 'Start Date',
CHANGE COLUMN `date_end` `date_end` date DEFAULT NULL COMMENT 'End Date',
DROP `convertion_rate`;

CREATE TABLE {$this->getTable('awardpoints_account2')} (
`account_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Account ID',
`store_id` smallint(5) unsigned NOT NULL COMMENT 'Store Id',
`customer_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'Customer ID',
`points_type` int(10) unsigned NOT NULL DEFAULT '1' COMMENT 'Type of Points',
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
ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Award Point Account Table';
 
INSERT INTO {$this->getTable('awardpoints_account2')} SELECT
account_id,
store_id,
customer_id,
points_type,
points_current,
points_spent,
order_id,
referral_id,
review_id,
date_start,
date_end
FROM {$this->getTable('awardpoints_account')};

DROP TABLE {$this->getTable('awardpoints_account')};

RENAME TABLE {$this->getTable('awardpoints_account2')} TO {$this->getTable('awardpoints_account')};

UPDATE {$this->getTable('awardpoints_account')} SET points_type = 2 WHERE order_id > 0;

SET FOREIGN_KEY_CHECKS=1; 
");

/**
 * Update 'awardpoints/referral'
 * Update Table Structure
 * Import previous Data
 */
$installer->run("
SET FOREIGN_KEY_CHECKS=0;

ALTER TABLE {$this->getTable('awardpoints_referral')} DROP FOREIGN KEY awardpoints_referral_parent_fk;
ALTER TABLE {$this->getTable('awardpoints_referral')} DROP FOREIGN KEY awardpoints_referral_child_fk1;

ALTER TABLE {$this->getTable('awardpoints_referral')} DROP INDEX email;
ALTER TABLE {$this->getTable('awardpoints_referral')} DROP INDEX son_id;
ALTER TABLE {$this->getTable('awardpoints_referral')} DROP INDEX FK_customer_entity;

ALTER TABLE {$this->getTable('awardpoints_referral')}
CHANGE COLUMN `awardpoints_referral_id` `referral_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Referral ID',
CHANGE COLUMN `awardpoints_referral_parent_id` `parent_id` int(10) unsigned NOT NULL COMMENT 'Referral Parent ID',
CHANGE COLUMN `awardpoints_referral_child_id` `child_id` int(10) unsigned DEFAULT NULL COMMENT 'Referral Child ID',
CHANGE COLUMN `awardpoints_referral_email` `child_email` varchar(255) DEFAULT NULL COMMENT 'Referral Child Email',
CHANGE COLUMN `awardpoints_referral_name` `child_name` varchar(255) DEFAULT NULL COMMENT 'Referral Child Full Name',
CHANGE COLUMN `awardpoints_referral_status` `referral_status` smallint(6) DEFAULT '0' COMMENT 'Referral Status'; 
 
CREATE TABLE {$this->getTable('awardpoints_referral2')} (
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
ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Award Point Referral Table'; 
 
INSERT INTO {$this->getTable('awardpoints_referral2')} SELECT
referral_id,
parent_id,
child_id,
child_email,
child_name,
referral_status
FROM {$this->getTable('awardpoints_referral')};

DROP TABLE {$this->getTable('awardpoints_referral')};

RENAME TABLE {$this->getTable('awardpoints_referral2')} TO {$this->getTable('awardpoints_referral')};

SET FOREIGN_KEY_CHECKS=1; 
");

/**
 * Update 'awardpoints/catalogrules'
 * Update Table Structure
 * Import previous Data
 */
$installer->run("
SET FOREIGN_KEY_CHECKS=0;

ALTER TABLE {$this->getTable('awardpoints_catalogrules')}
CHANGE COLUMN `rule_id` `rule_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Rule ID',
CHANGE COLUMN `title` `title` varchar(255) DEFAULT NULL COMMENT 'Rule Title',
ADD `description` text COMMENT 'Description',
CHANGE COLUMN `from_date` `from_date` date DEFAULT NULL COMMENT 'From Date',
CHANGE COLUMN `to_date` `to_date` date DEFAULT NULL COMMENT 'To Date',
CHANGE COLUMN `website_ids` `website_ids` text COMMENT 'Website Ids',
CHANGE COLUMN `customer_group_ids` `customer_group_ids` text COMMENT 'Customer Group Ids',
CHANGE COLUMN `action_type` `action_type` int(11) DEFAULT NULL COMMENT 'Action_type',
CHANGE COLUMN `status` `status` smallint(6) NOT NULL DEFAULT '0' COMMENT 'Rule Status',
CHANGE COLUMN `conditions_serialized` `conditions_serialized` mediumtext COMMENT 'Conditions Serialized',
CHANGE COLUMN `points` `points` int(11) DEFAULT NULL COMMENT 'Points',
CHANGE COLUMN `sort_order` `sort_order` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'Sort Order',
DROP `rule_type`;

CREATE TABLE {$this->getTable('awardpoints_catalogrules2')} (
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
ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Award Point Catalog Rules Table';
 
INSERT INTO {$this->getTable('awardpoints_catalogrules2')} SELECT
rule_id,
title,
description,
from_date,
to_date,
website_ids,
customer_group_ids,
action_type,
status,
conditions_serialized,
points,
sort_order
FROM {$this->getTable('awardpoints_catalogrules')};

DROP TABLE {$this->getTable('awardpoints_catalogrules')};

RENAME TABLE {$this->getTable('awardpoints_catalogrules2')} TO {$this->getTable('awardpoints_catalogrules')};

SET FOREIGN_KEY_CHECKS=1; 
");

/**
 * Update 'awardpoints/cartrules'
 * Update Table Structure
 * Import previous Data
 */
$installer->run("
SET FOREIGN_KEY_CHECKS=0;

ALTER TABLE {$this->getTable('awardpoints_pointrules')}
CHANGE COLUMN `rule_id` `rule_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Rule ID',
CHANGE COLUMN `title` `title` varchar(255) DEFAULT NULL COMMENT 'Rule Title',
ADD `description` text COMMENT 'Description',
CHANGE COLUMN `from_date` `from_date` date DEFAULT NULL COMMENT 'From Date',
CHANGE COLUMN `to_date` `to_date` date DEFAULT NULL COMMENT 'To Date',
CHANGE COLUMN `customer_group_ids` `customer_group_ids` text COMMENT 'Customer Group Ids',
CHANGE COLUMN `action_type` `action_type` int(11) DEFAULT NULL COMMENT 'Action_type',
CHANGE COLUMN `status` `status` smallint(6) NOT NULL DEFAULT '0' COMMENT 'Rule Status',
CHANGE COLUMN `conditions_serialized` `conditions_serialized` mediumtext COMMENT 'Conditions Serialized',
CHANGE COLUMN `points` `points` int(11) DEFAULT NULL COMMENT 'Points',
CHANGE COLUMN `website_ids` `website_ids` text COMMENT 'Website Ids',
CHANGE COLUMN `sort_order` `sort_order` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'Sort Order',
DROP `rule_type`;

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
ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Award Point Cart Rules Table';
 
INSERT INTO {$this->getTable('awardpoints_cartrules')} SELECT
rule_id,
title,
description,
from_date,
to_date,
customer_group_ids,
action_type,
status,
conditions_serialized,
points,
website_ids,
sort_order
FROM {$this->getTable('awardpoints_pointrules')};

DROP TABLE {$this->getTable('awardpoints_pointrules')};

SET FOREIGN_KEY_CHECKS=1; 
");

/**
 * Update 'awardpoints/rule'
 * Remove Deprecated Table
 */
$installer->run(" DROP TABLE IF EXISTS {$this->getTable('awardpoints_rule')}");

/**
 * End Installation Set-up
 */
$installer->endSetup();