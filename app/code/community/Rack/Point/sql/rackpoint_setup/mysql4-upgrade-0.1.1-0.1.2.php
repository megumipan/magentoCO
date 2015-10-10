<?php

/** @var $this Rack_Point_Model_Mysql4_Setup */

$this->startSetup()
     ->addAttribute('order', 'point_rate', array('type' => 'int', 'visible' => false, 'required' => false))
     ->run("
         ALTER TABLE `{$this->getTable('rackpoint/point_history')}` ADD COLUMN `rate` int(4) unsigned;
         ALTER TABLE `{$this->getTable('rackpoint/point_balance')}` ADD COLUMN `lastest_notify` datetime;
         ALTER TABLE `{$this->getTable('rackpoint/point_balance')}` ADD COLUMN `notify_times` int(1) unsigned;
     ");