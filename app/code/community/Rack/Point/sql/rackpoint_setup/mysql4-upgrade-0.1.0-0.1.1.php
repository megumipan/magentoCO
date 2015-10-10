<?php

/** @var $this Rack_Point_Model_Mysql4_Setup */

$this->startSetup()
     ->addAttribute('order_item', 'point_received_invoiced', array('type' => 'decimal', 'visible' => false, 'required' => false))
     ->addAttribute('order_item', 'point_received_refunded', array('type' => 'decimal', 'visible' => false, 'required' => false))
     ->addAttribute('invoice_item', 'point_received', array('type' => 'decimal', 'visible' => false, 'required' => false))
     ->run("
         ALTER TABLE `{$this->getTable('rackpoint/point_history')}`
         ADD COLUMN `ref_id` VARCHAR(50);
         
         ALTER TABLE `{$this->getTable('rackpoint/point_balance')}`
         ADD COLUMN `expired_at` DATETIME;
     ");