<?php

/** @var $this Rack_Point_Model_Mysql4_Setup */

$this->startSetup()
    ->run("
    ALTER TABLE `{$this->getTable('rackpoint/point_balance')}` ADD CONSTRAINT POINT_BALANCE_UQ UNIQUE (`customer_id`, `website_id`);
")->endSetup();