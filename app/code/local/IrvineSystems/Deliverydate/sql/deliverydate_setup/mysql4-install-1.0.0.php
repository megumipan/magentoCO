<?php
/*
 * Irvine Systems Delivery Date Optimum
 *
 * NOTICE OF LICENSE
 * This source file is subject to the Revised BSD License which is bundled with
 * this package in the files LICENSE.txt and LICENSE.html.
 *
 * @category	Magento Catalog Extension
 * @package		IrvineSystems_Deliverydate
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
 * Add the New columns for Delivery date and comments into
 */
// Define Order Table
$quoteAddressTable = $installer->getTable('sales/quote_address');
$orderTable = $installer->getTable('sales/order');
$orderGridTable = $installer->getTable('sales/order_grid');

// Add the columns to Quote Address Table
$installer->getConnection()->addColumn($quoteAddressTable, 'shipping_delivery_date', 'date');
$installer->getConnection()->addColumn($quoteAddressTable, 'shipping_delivery_time', 'varchar(30)');
$installer->getConnection()->addColumn($quoteAddressTable, 'shipping_delivery_comments', 'text');

// Add the columns to Order Table
$installer->getConnection()->addColumn($orderTable, 'shipping_delivery_date', 'date');
$installer->getConnection()->addColumn($orderTable, 'shipping_delivery_time', 'varchar(30)');
$installer->getConnection()->addColumn($orderTable, 'shipping_delivery_comments', 'text');

// Add the columns to Order Grid Table
$installer->getConnection()->addColumn($orderGridTable, 'shipping_delivery_date', 'date');
// Add an Index for the Delivery date in Grid
$installer->getConnection()->addKey($orderGridTable, 'shipping_delivery_date_idx', 'shipping_delivery_date');

/**
 * End Installation Set-up
 */
$installer->endSetup(); 