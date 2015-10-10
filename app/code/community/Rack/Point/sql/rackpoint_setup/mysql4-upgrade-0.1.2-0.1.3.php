<?php

/** @var $this Rack_Point_Model_Mysql4_Setup */

$this->startSetup()
     ->addAttribute('order', 'receive_point_mode', array('type' => 'int', 'visible' => false, 'required' => false))
     ->addAttribute('quote', 'receive_point_mode', array('type' => 'int', 'visible' => false, 'required' => false));