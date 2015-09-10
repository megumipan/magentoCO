<?php

$this->startSetup()
  ->addAttribute('quote', 'delivery_date', array('type' => 'varchar', 'visible' => false, 'required' => false))
  ->addAttribute('order', 'delivery_date', array('type' => 'varchar', 'visible' => false, 'required' => false))
  ->addAttribute('quote', 'timeslot', array('type' => 'varchar', 'visible' => false, 'required' => false))
  ->addAttribute('order', 'timeslot', array('type' => 'varchar', 'visible' => false, 'required' => false))
  ->endSetup();
