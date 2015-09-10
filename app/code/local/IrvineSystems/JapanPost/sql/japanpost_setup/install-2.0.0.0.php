<?php
/*
 * Irvine Systems Shipping Japan Jp
 *
 * NOTICE OF LICENSE
 * This source file is subject to the Revised BSD License which is bundled with
 * this package in the files LICENSE.txt and LICENSE.html.
 *
 * @category	Magento Shipping Extension
 * @package		IrvineSystems_JapanPost
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
 * Common Definitions
 */
$connection = $installer->getConnection();

// Define the Entity set
$entityTypeId = $installer->getEntityTypeId('catalog_product');

// Create and add the attribute 'pkg_depth'
$installer->addAttribute('catalog_product', 'pkg_depth', array(
             'label'             => 'Package Depth (mm)',
             'type'              => 'int',
             'input'             => 'text',
             'backend'           => '',
             'frontend'          => '', 
             'frontend_class'	 => 'validate-digits', 
             'default_value'	 => '1',
             'global'            => true,
             'visible'           => true,
             'required'          => true,
             'user_defined'      => true,
             'searchable'        => false,
             'filterable'        => false,
             'comparable'        => false,
             'visible_on_front'  => false,
             'visible_in_advanced_search' => false,
             'unique'            => false
));

// Create and add the attribute 'pkg_height'
$installer->addAttribute('catalog_product', 'pkg_height', array(
             'label'             => 'Package Height (mm)',
             'type'              => 'int',
             'input'             => 'text',
             'backend'           => '',
             'frontend'          => '', 
             'frontend_class'	 => 'validate-digits', 
             'default_value'	 => '1',
             'global'            => true,
             'visible'           => true,
             'required'          => true,
             'user_defined'      => true,
             'searchable'        => false,
             'filterable'        => false,
             'comparable'        => false,
             'visible_on_front'  => false,
             'visible_in_advanced_search' => false,
             'unique'            => false
));

// Create and add the attribute 'pkg_width'
$installer->addAttribute('catalog_product', 'pkg_width', array(
             'label'             => 'Package Width (mm)',
             'type'              => 'int',
             'input'             => 'text',
             'backend'           => '',
             'frontend'          => '', 
             'frontend_class'	 => 'validate-digits', 
             'default_value'	 => '1',
             'global'            => true,
             'visible'           => true,
             'required'          => true,
             'user_defined'      => true,
             'searchable'        => false,
             'filterable'        => false,
             'comparable'        => false,
             'visible_on_front'  => false,
             'visible_in_advanced_search' => false,
             'unique'            => false
));

// Asign the nerw asttributes we just created to all available product sets
$attributeIds = array(
	$installer->getAttributeId('catalog_product', 'pkg_depth'),
	$installer->getAttributeId('catalog_product', 'pkg_height'),
	$installer->getAttributeId('catalog_product', 'pkg_width')
	);

foreach ($attributeIds as $attributeId) {
	foreach ($installer->getAllAttributeSetIds('catalog_product') as $attributeSetId) {
	    try {
			// Add the attribut to the General Group
	        $attributeGroupId = $installer->getAttributeGroupId('catalog_product', $attributeSetId, 'General');
	    } catch (Exception $e) {
			// If the General Group is unavalable add the attribute without group
			// This happen because certain sets can be created whitout a Attribute Group
	        $attributeGroupId = $installer->getDefaultAttributeGroupId('catalog_product', $attributeSetId);
	    }
	    $installer->addAttributeToSet('catalog_product', $attributeSetId, $attributeGroupId, $attributeId);
	}
}

/** ====================================================================================== **/
/** ============================= CREATE SLIPDATABASE TABLE ============================== **/
/** ====================================================================================== **/
$table = $installer->getConnection()
    ->newTable($installer->getTable('japanpost/slips'))
	// Slips Primary Key
    ->addColumn('slip_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Account ID')
	// Order Information
    ->addColumn('order_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Order Number')
    ->addColumn('tracking_number', Varien_Db_Ddl_Table::TYPE_TEXT, 15, array(
        ), 'Tracking Number')
	// Customer Data
    ->addColumn('customer_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Customer ID')
    ->addColumn('customer_prefix', Varien_Db_Ddl_Table::TYPE_TINYINT, null, array(
        'default'   => '9',
        ), 'Customer Prefix')
    ->addColumn('customer_name', Varien_Db_Ddl_Table::TYPE_TEXT, 72, array(
        ), 'Customer Full Name')
    ->addColumn('customer_namekana', Varien_Db_Ddl_Table::TYPE_TEXT, 80, array(
        ), 'Customer Full Name (Kana)')
    ->addColumn('customer_address', Varien_Db_Ddl_Table::TYPE_TEXT, 120, array(
        ), 'Customer Full Address')
    ->addColumn('customer_postcode', Varien_Db_Ddl_Table::TYPE_TEXT, 10, array(
        ), 'Customer Post Code')
    ->addColumn('customer_tel', Varien_Db_Ddl_Table::TYPE_TEXT, 50, array(
        ), 'Customer Telephone Number')
    ->addColumn('customer_email', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        ), 'Customer Email')
    ->addColumn('notification_post', Varien_Db_Ddl_Table::TYPE_TINYINT, null, array(
        'nullable'  => false,
        'default'   => '0',
        ), 'Post Notification')
    ->addColumn('notification_email', Varien_Db_Ddl_Table::TYPE_TINYINT, null, array(
        'nullable'  => false,
        'default'   => '0',
        ), 'Email Notification')
	// Store Data
    ->addColumn('store_memberid', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Store Member ID')
    ->addColumn('store_prefix', Varien_Db_Ddl_Table::TYPE_TINYINT, null, array(
        'nullable'  => false,
        'default'   => '9',
        ), 'Store Prefix')
    ->addColumn('store_name', Varien_Db_Ddl_Table::TYPE_TEXT, 72, array(
        ), 'Store Full Name')
    ->addColumn('store_namekana', Varien_Db_Ddl_Table::TYPE_TEXT, 80, array(
        ), 'Store Full Name (Kana)')
    ->addColumn('store_address', Varien_Db_Ddl_Table::TYPE_TEXT, 120, array(
        ), 'Store Full Address')
    ->addColumn('store_postcode', Varien_Db_Ddl_Table::TYPE_TEXT, 10, array(
        ), 'Store Post Code')
    ->addColumn('store_tel', Varien_Db_Ddl_Table::TYPE_TEXT, 50, array(
        ), 'Store Telephone Number')
    ->addColumn('store_email', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        ), 'Store Email')
	// Package Statuses
    ->addColumn('fragile_status', Varien_Db_Ddl_Table::TYPE_TINYINT, null, array(
        'nullable'  => false,
        'default'   => '0',
        ), 'Fragile Shipping')
    ->addColumn('creature_status', Varien_Db_Ddl_Table::TYPE_TINYINT, null, array(
        'nullable'  => false,
        'default'   => '0',
        ), 'Creature Shipping')
    ->addColumn('glass_status', Varien_Db_Ddl_Table::TYPE_TINYINT, null, array(
        'nullable'  => false,
        'default'   => '0',
        ), 'Glass Shipping')
    ->addColumn('side_status', Varien_Db_Ddl_Table::TYPE_TINYINT, null, array(
        'nullable'  => false,
        'default'   => '0',
        ), 'Side Shipping')
    ->addColumn('weight_status', Varien_Db_Ddl_Table::TYPE_TINYINT, null, array(
        'nullable'  => false,
        'default'   => '0',
        ), 'Weight Shipping')
    ->addColumn('ship_cooler', Varien_Db_Ddl_Table::TYPE_TINYINT, null, array(
        'nullable'  => false,
        'default'   => '0',
        ), 'Cooling Shipment')
    ->addColumn('package_weight', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Package Weight')
    ->addColumn('package_size', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Package Size')
	// Delivery Information
    ->addColumn('product_number', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        ), 'Product Number')
    ->addColumn('product_name', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        ), 'Product Name')
    ->addColumn('delivery_mode', Varien_Db_Ddl_Table::TYPE_TINYINT, null, array(
        'nullable'  => false,
        'default'   => '0',
        ), 'Delivery Mode')
    ->addColumn('shipping_date', Varien_Db_Ddl_Table::TYPE_DATE, null, array(
        ), 'Extimate Shipping Date')
    ->addColumn('delivery_date', Varien_Db_Ddl_Table::TYPE_DATE, null, array(
        ), 'Delivery Date')
    ->addColumn('delivery_time', Varien_Db_Ddl_Table::TYPE_TEXT, 15, array(
        ), 'Delivery Time')
    ->addColumn('delivery_type', Varien_Db_Ddl_Table::TYPE_TINYINT, null, array(
        'nullable'  => false,
        'default'   => '1',
        ), 'Delivery Type')
    ->addColumn('delivery_comment', Varien_Db_Ddl_Table::TYPE_TEXT, 60, array(
        ), 'Discount Comment')
    ->addColumn('free_field2', Varien_Db_Ddl_Table::TYPE_TEXT, 60, array(
        ), 'Free Field 2')
    ->addColumn('free_field3', Varien_Db_Ddl_Table::TYPE_TEXT, 60, array(
        ), 'Free Field 3')
    ->addColumn('free_field4', Varien_Db_Ddl_Table::TYPE_TEXT, 60, array(
        ), 'Free Field 4')
    ->addColumn('free_field5', Varien_Db_Ddl_Table::TYPE_TEXT, 60, array(
        ), 'Free Field 5')
	// Cash on Delivery Information
    ->addColumn('cod_status', Varien_Db_Ddl_Table::TYPE_TINYINT, null, array(
        'nullable'  => false,
        'default'   => '0',
        ), 'Cash on Delivery Status')
    ->addColumn('cod_amount', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Cash on Delivery Amount')
	// Others
    ->addColumn('payment_source', Varien_Db_Ddl_Table::TYPE_TINYINT, null, array(
        'nullable'  => false,
        'default'   => '0',
        ), 'Payment Source')
    ->addColumn('mail_class', Varien_Db_Ddl_Table::TYPE_TINYINT, null, array(
        'nullable'  => false,
        'default'   => '9',
        ), 'Mail Class')
    ->addColumn('ship_service', Varien_Db_Ddl_Table::TYPE_TINYINT, null, array(
        'nullable'  => false,
        'default'   => '0',
        ), 'Shipping Extra Service')
    ->addColumn('ensured_amount', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        ), 'Ensured Amount')
    ->addColumn('discount_type', Varien_Db_Ddl_Table::TYPE_TINYINT, null, array(
        'nullable'  => false,
        'default'   => '0',
        ), 'Discount Type')
    ->addColumn('taxable', Varien_Db_Ddl_Table::TYPE_TINYINT, null, array(
        'nullable'  => false,
        'default'   => '1',
        ), 'Taxable Shipment')
    ->addColumn('sort_code', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        ), 'Sort Code')
    // Additional Table Indexes beside the primary
	->addIndex($installer->getIdxName('japanpost/slips', array('order_id')),
        array('order_id'))
    ->addIndex($installer->getIdxName('japanpost/slips', array('customer_id')),
        array('customer_id'))
    // Table Comment
	->setComment('Japan Post Slips Data Table');
$installer->getConnection()->createTable($table);

/** ====================================================================================== **/
/** =============================== ADDING Japanese regions ============================== **/
/** ====================================================================================== **/

/**
 * Directory Definition
 */
// Database Tables
$regionTable = $installer->getTable('directory/country_region');
$regionTableName = $installer->getTable('directory/country_region_name');

// Japanese Regions
$country_id = 'JP';
$japanRegions = array(
    array('JP', 'Hokkaido', '北海道'),
    array('JP', 'Aomori', '青森県'),
    array('JP', 'Iwate', '岩手県'),
    array('JP', 'Miyagi', '宮城県'),
    array('JP', 'Akita', '秋田県'),
    array('JP', 'Yamagata', '山形県'),
    array('JP', 'Fukushima', '福島県'),
    array('JP', 'Ibaragi', '茨城県'),
    array('JP', 'Tochigi', '栃木県'),
    array('JP', 'Gunma', '群馬県'),
    array('JP', 'Saitama', '埼玉県'),
    array('JP', 'Chiba', '千葉県'),
    array('JP', 'Tokyo', '東京都'),
    array('JP', 'Kanagawa', '神奈川県'),
    array('JP', 'Niigata', '新潟県'),
    array('JP', 'Toyama', '富山県'),
    array('JP', 'Ishikawa', '石川県'),
    array('JP', 'Fukui', '福井県'),
    array('JP', 'Yamanashi', '山梨県'),
    array('JP', 'Nagano', '長野県'),
    array('JP', 'Gifu', '岐阜県'),
    array('JP', 'Shizuoka', '静岡県'),
    array('JP', 'Aichi', '愛知県'),
    array('JP', 'Mie', '三重県'),
    array('JP', 'Shiga', '滋賀県'),
    array('JP', 'Kyoto', '京都府'),
    array('JP', 'Osaka', '大阪府'),
    array('JP', 'Hyogo', '兵庫県'),
    array('JP', 'Nara', '奈良県'),
    array('JP', 'Wakayama', '和歌山県'),
    array('JP', 'Tottori', '鳥取県'),
    array('JP', 'Shimane', '島根県'),
    array('JP', 'Okayama', '岡山県'),
    array('JP', 'Hiroshima', '広島県'),
    array('JP', 'Yamaguchi', '山口県'),
    array('JP', 'Tokushima', '徳島県'),
    array('JP', 'Kagawa', '香川県'),
    array('JP', 'Ehime', '愛媛県'),
    array('JP', 'Kochi', '高知県'),
    array('JP', 'Fukuoka', '福岡県'),
    array('JP', 'Saga', '佐賀県'),
    array('JP', 'Nagasaki', '長崎県'),
    array('JP', 'Kumamoto', '熊本県'),
    array('JP', 'Oita', '大分県'),
    array('JP', 'Miyazaki', '宮崎県'),
    array('JP', 'Kagoshima', '鹿児島県'),
    array('JP', 'Okinawa', '沖縄県'));

/**
 * Update the 'directory/country_region' table with the New Japanese Regions if the regions do not already exist
 */
foreach ($japanRegions as $row) {
    if (! ($connection->fetchOne("SELECT 1 FROM `{$regionTable}` WHERE `country_id` = :country_id && `code` = :code", array('country_id' => $row[0], 'code' => $row[1])))) {
        $connection->insert($regionTable, array(
            'country_id'   => $row[0],
            'code'         => $row[1],
            'default_name' => $row[1]
        ));
    } 
}

/**
 * Add the Default en_US locale for the Japanese Regions which we just added to the Database
 */
$select = $connection->select()
    ->from(array('a' => $regionTable), array(
        new Zend_Db_Expr('"en_US"'),
        'region_id',
        'default_name'
		))
    ->where('a.country_id = ?', $country_id);

// Generate the Query
$query = $select->insertFromSelect($regionTableName, array('locale', 'region_id', 'name'));

// Execute the Query
$connection->query($query);

/**
 * Add the ja_JP locale for the Japanese Regions which we just added to the Database
 */
foreach ($japanRegions as $row) {
	$select = $connection->select()
	    ->from(array('a' => $regionTable), array(
	        new Zend_Db_Expr('"ja_JP"'),
	        'region_id',
	        new Zend_Db_Expr('"' .$row[2] .'"')
			))
	    ->where('a.default_name = ?', $row[1]);

	// Generate the Query
	$query = $select->insertFromSelect($regionTableName, array('locale', 'region_id', 'name'));

	// Execute the Query
	$connection->query($query);
}

/** ====================================================================================== **/
/** ========================== END ADDING Japanese regions =============================== **/
/** ====================================================================================== **/

/**
 * End Installation Set-up
 */
$installer->endSetup();