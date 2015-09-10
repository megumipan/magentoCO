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
$table = $installer->getConnection()
    ->newTable($installer->getTable('awardpoints/account'))
    ->addColumn('account_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Account ID')
    ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        ), 'Store Id')
    ->addColumn('customer_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Customer ID')
    ->addColumn('points_type', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array( // NEWLY IMPLEMENTED
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Type of Points')
    ->addColumn('points_current', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Current Points')
    ->addColumn('points_spent', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Spent Points')
    ->addColumn('order_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Order ID')
    ->addColumn('referral_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Referral ID')
    ->addColumn('review_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array( // NEWLY IMPLEMENTED
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Review ID')
    ->addColumn('date_start', Varien_Db_Ddl_Table::TYPE_DATE, null, array(
        ), 'Start Date')
    ->addColumn('date_end', Varien_Db_Ddl_Table::TYPE_DATE, null, array(
        ), 'End Date')
	->addIndex($installer->getIdxName('awardpoints/account', array('order_id')), array('order_id'))
	->addIndex($installer->getIdxName('awardpoints/account', array('customer_id')), array('customer_id'))
    ->setComment('Award Point Account Table');
$installer->getConnection()->createTable($table);

/**
 * Create table 'awardpoints/referral'
 * Award Point Table for Referral Points Management
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('awardpoints/referral'))
    ->addColumn('referral_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Referral ID')
    ->addColumn('parent_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        ), 'Referral Parent ID')
    ->addColumn('child_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        ), 'Referral Child ID')
    ->addColumn('child_email', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        ), 'Referral Child Email')
    ->addColumn('child_name', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        'nullable'  => true,
        ), 'Referral Child Full Name')
    ->addColumn('referral_status', Varien_Db_Ddl_Table::TYPE_TINYINT, null, array(
        'default'   => '0',
        ), 'Referral Status')
    ->addIndex($installer->getIdxName('awardpoints/referral', array('child_id'), Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE),
        array('child_id'), array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE))
    ->addIndex($installer->getIdxName('awardpoints/referral', array('child_email'), Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE),
        array('child_email'), array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE))
	->addIndex($installer->getIdxName('awardpoints/referral', array('parent_id')), array('parent_id'))
    ->setComment('Award Point Referral Table');
$installer->getConnection()->createTable($table);

/**
 * Create table 'awardpoints/catalogrules'
 * Award Point Table for Catalog Points Rules Management
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('awardpoints/catalogrules'))
    ->addColumn('rule_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Rule ID')
    ->addColumn('title', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        ), 'Rule Title')
    ->addColumn('description', Varien_Db_Ddl_Table::TYPE_TEXT, '64k', array( // NEWLY IMPLEMENTED
        ), 'Description')
    ->addColumn('from_date', Varien_Db_Ddl_Table::TYPE_DATE, null, array(
        ), 'From Date')
    ->addColumn('to_date', Varien_Db_Ddl_Table::TYPE_DATE, null, array(
        ), 'To Date')
    ->addColumn('website_ids', Varien_Db_Ddl_Table::TYPE_TEXT, 4000, array(
        ), 'Website Ids')
    ->addColumn('customer_group_ids', Varien_Db_Ddl_Table::TYPE_TEXT, '64k', array(
        ), 'Customer Group Ids')
    ->addColumn('action_type', Varien_Db_Ddl_Table::TYPE_INTEGER, array(
        'nullable'  => false,
        'default'   => '0',
        ), 'Action Type')
    ->addColumn('status', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'nullable'  => false,
        'default'   => '0',
        ), 'Rule Status')
    ->addColumn('conditions_serialized', Varien_Db_Ddl_Table::TYPE_TEXT, '2M', array(
        ), 'Conditions Serialized')
	->addColumn('points', Varien_Db_Ddl_Table::TYPE_INTEGER, array(
        'nullable'  => false,
        ), 'Points')
    ->addColumn('sort_order', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Sort Order')
    ->setComment('Award Point Catalog Rules Table');
$installer->getConnection()->createTable($table);

/**
 * Create table 'awardpoints/cartrules'
 * Award Point Table for Cart Points Rules Management
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('awardpoints/cartrules'))
    ->addColumn('rule_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Rule ID')
    ->addColumn('title', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        ), 'Rule Title')
    ->addColumn('description', Varien_Db_Ddl_Table::TYPE_TEXT, '64k', array( // NEWLY IMPLEMENTED
        ), 'Description')
    ->addColumn('from_date', Varien_Db_Ddl_Table::TYPE_DATE, null, array(
        ), 'From Date')
    ->addColumn('to_date', Varien_Db_Ddl_Table::TYPE_DATE, null, array(
        ), 'To Date')
    ->addColumn('website_ids', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        'unsigned'  => true,
        ), 'Website Ids')
    ->addColumn('customer_group_ids', Varien_Db_Ddl_Table::TYPE_TEXT, '64k', array(
        ), 'Customer Group Ids')
    ->addColumn('action_type', Varien_Db_Ddl_Table::TYPE_INTEGER, array(
        'nullable'  => false,
        ), 'Action Type')
    ->addColumn('status', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'nullable'  => false,
        'default'   => '0',
        ), 'Rule Status')
    ->addColumn('conditions_serialized', Varien_Db_Ddl_Table::TYPE_TEXT, '2M', array(
        ), 'Conditions Serialized')
    ->addColumn('website_ids', Varien_Db_Ddl_Table::TYPE_TEXT, 4000, array( // NEWLY IMPLEMENTED
        ), 'Website Ids')
	->addColumn('points', Varien_Db_Ddl_Table::TYPE_INTEGER, array(
        'nullable'  => false,
        ), 'Points')
    ->addColumn('sort_order', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Sort Order')
    ->setComment('Award Point Cart Rules Table');
$installer->getConnection()->createTable($table);

/**
 * End Installation Set-up
 */
$installer->endSetup();