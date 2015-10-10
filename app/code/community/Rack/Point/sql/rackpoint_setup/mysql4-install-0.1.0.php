<?php

/** @var $this Rack_Point_Model_Mysql4_Setup */

$this->startSetup()
    ->addAttribute('order', 'point_received', array('type' => 'int', 'visible' => false, 'required' => false))
    ->addAttribute('order', 'point_received_canceled', array('type' => 'int', 'visible' => false, 'required' => false))
    ->addAttribute('order', 'point_received_invoiced', array('type' => 'int', 'visible' => false, 'required' => false))
    ->addAttribute('order', 'point_received_refunded', array('type' => 'int', 'visible' => false, 'required' => false))
    ->addAttribute('order', 'point_used', array('type' => 'int', 'visible' => false, 'required' => false))
    ->addAttribute('order', 'point_used_canceled', array('type' => 'int', 'visible' => false, 'required' => false))
    ->addAttribute('order', 'point_used_invoiced', array('type' => 'int', 'visible' => false, 'required' => false))
    ->addAttribute('order', 'point_used_refunded', array('type' => 'int', 'visible' => false, 'required' => false))
    ->addAttribute('order_item', 'point_received', array('type' => 'decimal', 'visible' => false, 'required' => false))
    ->addAttribute('quote', 'point_received', array('type' => 'int', 'visible' => false, 'required' => false))
    ->addAttribute('quote', 'point_used', array('type' => 'int', 'visible' => false, 'required' => false))
    ->addAttribute('quote_address', 'point_received', array('type' => 'decimal', 'visible' => false, 'required' => false))
    ->addAttribute('quote_address', 'point_used', array('type' => 'decimal', 'visible' => false, 'required' => false))
    ->addAttribute('invoice', 'point_used', array('type' => 'int', 'visible' => false, 'required' => false))
    ->addAttribute('invoice', 'point_received', array('type' => 'int', 'visible' => false, 'required' => false))
    ->addAttribute('creditmemo', 'point_used', array('type' => 'int', 'visible' => false, 'required' => false))
    ->addAttribute('creditmemo', 'point_received', array('type' => 'int', 'visible' => false, 'required' => false))
    ->run("
    CREATE TABLE IF NOT EXISTS `{$this->getTable('rackpoint/point_balance')}` (
        `id`    INT UNSIGNED PRIMARY KEY auto_increment,
        `customer_id` int(10) unsigned default NULL,
        `website_id`      SMALLINT(5) UNSIGNED,
        `balance`       INT,
        `created_at`    DATETIME,
        `updated_at`    DATETIME,
        CONSTRAINT  FOREIGN KEY (`customer_id`) REFERENCES `{$this->getTable('customer/entity')}`(`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE,
        CONSTRAINT  FOREIGN KEY (`website_id`) REFERENCES {$this->getTable('core_website')} (`website_id`) ON DELETE CASCADE ON UPDATE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
            
    CREATE TABLE IF NOT EXISTS  `{$this->getTable('rackpoint/point_history')}` (
        `id`    INT UNSIGNED PRIMARY KEY auto_increment,
        `customer_id` int(10) unsigned default NULL,
        `website_id`      SMALLINT(5) UNSIGNED,
        `action`        VARCHAR(20),
        `point`         INT,
        `comment`       VARCHAR(255),
        `created_at`    DATETIME,
        CONSTRAINT `FK_CUSTOMER_POINT_HISTORY_CUSTOMER` FOREIGN KEY (`customer_id`) REFERENCES `{$this->getTable('customer/entity')}`(`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE,
        CONSTRAINT `FK_CUSTOMER_POINT_HISTORY_STORE` FOREIGN KEY (`website_id`) REFERENCES {$this->getTable('core_website')} (`website_id`) ON DELETE CASCADE ON UPDATE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
    
    CREATE TABLE IF NOT EXISTS  `{$this->getTable('rackpoint/catalog_rule')}` (
      `rule_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
      `name` varchar(255) NOT NULL DEFAULT '',
      `description` text NOT NULL,
      `from_date` date DEFAULT NULL,
      `to_date` date DEFAULT NULL,
      `customer_group_ids` text,
      `is_active` tinyint(1) NOT NULL DEFAULT '0',
      `conditions_serialized` mediumtext NOT NULL,
      `actions_serialized` mediumtext NOT NULL,
      `stop_rules_processing` tinyint(1) NOT NULL DEFAULT '1',
      `sort_order` int(10) unsigned NOT NULL DEFAULT '0',
      `simple_action` varchar(32) NOT NULL,
      `point_amount` decimal(12,2) NOT NULL,
      `website_ids` text,
      PRIMARY KEY (`rule_id`),
      KEY `sort_order` (`is_active`,`sort_order`,`to_date`,`from_date`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
    
    CREATE TABLE IF NOT EXISTS `{$this->getTable('rackpoint/rule_product')}` (
      `rule_product_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
      `rule_id` int(10) unsigned NOT NULL DEFAULT '0',
      `from_time` int(10) unsigned NOT NULL DEFAULT '0',
      `to_time` int(10) unsigned NOT NULL DEFAULT '0',
      `customer_group_id` smallint(5) unsigned NOT NULL DEFAULT '0',
      `product_id` int(10) unsigned NOT NULL DEFAULT '0',
      `action_operator` enum('to_fixed','to_percent','by_fixed','by_percent') NOT NULL DEFAULT 'to_fixed',
      `action_amount` decimal(12,4) NOT NULL DEFAULT '0.0000',
      `action_stop` tinyint(1) NOT NULL DEFAULT '0',
      `sort_order` int(10) unsigned NOT NULL DEFAULT '0',
      `website_id` smallint(5) unsigned NOT NULL,
      PRIMARY KEY (`rule_product_id`),
      UNIQUE KEY `sort_order` (`rule_id`,`from_time`,`to_time`,`website_id`,`customer_group_id`,`product_id`,`sort_order`),
      KEY `FK_rackpoint_rule_product_rule` (`rule_id`),
      KEY `FK_rackpoint_rule_product_customergroup` (`customer_group_id`),
      KEY `FK_rackpoint_rule_product_website` (`website_id`),
      KEY `FK_RACKPOINT_RULE_PRODUCT_PRODUCT` (`product_id`),
      KEY `IDX_FROM_TIME` (`from_time`),
      KEY `IDX_TO_TIME` (`to_time`),
      CONSTRAINT `FK_rackpoint_rule_product_customergroup` FOREIGN KEY (`customer_group_id`) REFERENCES `{$this->getTable('customer/customer_group')}` (`customer_group_id`) ON DELETE CASCADE ON UPDATE CASCADE,
      CONSTRAINT `FK_rackpoint_rule_product_product` FOREIGN KEY (`product_id`) REFERENCES `{$this->getTable('catalog/product')}` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE,
      CONSTRAINT `FK_rackpoint_rule_product_rule` FOREIGN KEY (`rule_id`) REFERENCES `{$this->getTable('rackpoint/catalog_rule')}` (`rule_id`) ON DELETE CASCADE ON UPDATE CASCADE,
      CONSTRAINT `FK_rackpoint_rule_product_website` FOREIGN KEY (`website_id`) REFERENCES `{$this->getTable('core/website')}` (`website_id`) ON DELETE CASCADE ON UPDATE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

")->endSetup();