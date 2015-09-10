<?php
/*
 * Irvine Systems Shipping Japan Sgw
 *
 * NOTICE OF LICENSE
 * This source file is subject to the Revised BSD License which is bundled with
 * this package in the files LICENSE.txt and LICENSE.html.
 *
 * @category	Magento Shipping Extension
 * @package		IrvineSystems_Sagawa
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
$table = $connection
    ->newTable($installer->getTable('sagawa/slips'))
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
    ->addColumn('customer_id', Varien_Db_Ddl_Table::TYPE_TEXT, 12, array(
        ), 'Customer ID')
    ->addColumn('customer_member_id', Varien_Db_Ddl_Table::TYPE_TEXT, 16, array(
        ), 'Customer Management Number')
    ->addColumn('customer_address_code', Varien_Db_Ddl_Table::TYPE_TEXT, 12, array(
        ), 'Customer Address Book Code')
    ->addColumn('customer_name', Varien_Db_Ddl_Table::TYPE_TEXT, 20, array(
        ), 'Customer Full Name')
    ->addColumn('customer_namekana', Varien_Db_Ddl_Table::TYPE_TEXT, 20, array(
        ), 'Customer Full Name (Kana)')
    ->addColumn('customer_address_1', Varien_Db_Ddl_Table::TYPE_TEXT, 20, array(
        ), 'Customer Address 1')
    ->addColumn('customer_address_2', Varien_Db_Ddl_Table::TYPE_TEXT, 20, array(
        ), 'Customer Address 2')
    ->addColumn('customer_address_3', Varien_Db_Ddl_Table::TYPE_TEXT, 20, array(
        ), 'Customer Address 3')
    ->addColumn('customer_postcode', Varien_Db_Ddl_Table::TYPE_TEXT, 10, array(
        ), 'Customer Post Code')
    ->addColumn('customer_tel', Varien_Db_Ddl_Table::TYPE_TEXT, 15, array(
        ), 'Customer Telephone Number')
	// Store Data
    ->addColumn('store_name', Varien_Db_Ddl_Table::TYPE_TEXT, 72, array(
        ), 'Store Full Name')
    ->addColumn('store_namekana', Varien_Db_Ddl_Table::TYPE_TEXT, 80, array(
        ), 'Store Full Name (Kana)')
    ->addColumn('store_contact', Varien_Db_Ddl_Table::TYPE_TEXT, 20, array(
        ), 'Store Contact')
    ->addColumn('store_address_1', Varien_Db_Ddl_Table::TYPE_TEXT, 20, array(
        ), 'Store Address 1')
    ->addColumn('store_address_2', Varien_Db_Ddl_Table::TYPE_TEXT, 20, array(
        ), 'Store Address 2')
    ->addColumn('store_postcode', Varien_Db_Ddl_Table::TYPE_TEXT, 10, array(
        ), 'Store Post Code')
    ->addColumn('shipper_tel', Varien_Db_Ddl_Table::TYPE_TEXT, 15, array(
        ), 'Shipper Telephone Number')
    ->addColumn('store_tel', Varien_Db_Ddl_Table::TYPE_TEXT, 15, array(
        ), 'Store Telephone Number')
	// Order Information
    ->addColumn('packing_code', Varien_Db_Ddl_Table::TYPE_TEXT, 3, array(
        'nullable'  => false,
        'default'   => '001',
        ), 'Packing Code')
    ->addColumn('product_name_1', Varien_Db_Ddl_Table::TYPE_TEXT, 32, array(
        ), 'Product Name 1')
    ->addColumn('product_name_2', Varien_Db_Ddl_Table::TYPE_TEXT, 32, array(
        ), 'Product Name 1')
    ->addColumn('product_name_3', Varien_Db_Ddl_Table::TYPE_TEXT, 32, array(
        ), 'Product Name 1')
    ->addColumn('product_name_4', Varien_Db_Ddl_Table::TYPE_TEXT, 32, array(
        ), 'Product Name 1')
    ->addColumn('product_name_5', Varien_Db_Ddl_Table::TYPE_TEXT, 32, array(
        ), 'Product Name 1')
    ->addColumn('packages_number', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '1',
        ), 'Number of Packages')
    ->addColumn('tax_amount', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        ), 'Tax Amount')
	// Cash on Delivery Information
    ->addColumn('cod_amount', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Cash on Delivery Amount')
    ->addColumn('cod_method', Varien_Db_Ddl_Table::TYPE_TINYINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Cash on Delivery PAyment method')
	// Package Statuses
    ->addColumn('fragile_status', Varien_Db_Ddl_Table::TYPE_TINYINT, null, array(
        'nullable'  => false,
        'default'   => '0',
        ), 'Fragile Shipping')
    ->addColumn('valuables_status', Varien_Db_Ddl_Table::TYPE_TINYINT, null, array(
        'nullable'  => false,
        'default'   => '0',
        ), 'Valuables')
    ->addColumn('side_status', Varien_Db_Ddl_Table::TYPE_TINYINT, null, array(
        'nullable'  => false,
        'default'   => '0',
        ), 'Side Shipping')
	// Delivery Information
    ->addColumn('ship_method', Varien_Db_Ddl_Table::TYPE_TEXT, 3, array(
        'default'   => '000',
        ), 'Shipping Method')
    ->addColumn('cooling_shipment', Varien_Db_Ddl_Table::TYPE_TEXT, 3, array(
        'nullable'  => false,
        'default'   => '001',
        ), 'Cooling Shipment')
    ->addColumn('delivery_date', Varien_Db_Ddl_Table::TYPE_TEXT, 8, array(
        ), 'Delivery Date')
    ->addColumn('delivery_time', Varien_Db_Ddl_Table::TYPE_TEXT, 2, array(
        'nullable'  => false,
        'default'   => '01',
        ), 'Delivery Time')
    ->addColumn('delivery_time_min', Varien_Db_Ddl_Table::TYPE_TEXT, 4, array(
        ), 'Delivery Time Minutes')
    ->addColumn('delivery_type', Varien_Db_Ddl_Table::TYPE_TINYINT, null, array(
        ), 'Delivery Type')
    ->addColumn('src_class', Varien_Db_Ddl_Table::TYPE_TINYINT, null, array(
        ), 'SRC Classification')
    ->addColumn('branc_code', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        ), 'Branch Code')
    ->addColumn('ensured_amount', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Ensured Amount')
    ->addColumn('ensured_amount_printed', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Ensured Printed Amount')
    ->addColumn('service_code_1', Varien_Db_Ddl_Table::TYPE_TEXT, 3, array(
        ), 'Specified Service Code 1')
    ->addColumn('service_code_2', Varien_Db_Ddl_Table::TYPE_TEXT, 3, array(
        ), 'Specified Service Code 2')
    ->addColumn('service_code_3', Varien_Db_Ddl_Table::TYPE_TEXT, 3, array(
        ), 'Specified Service Code 3')
    ->addColumn('payment_source', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Delivery Payment Source')
    // Additional Table Indexes beside the primary
	->addIndex($installer->getIdxName('sagawa/slips', array('order_id')),
        array('order_id'))
    ->addIndex($installer->getIdxName('sagawa/slips', array('customer_id')),
        array('customer_id'))
    // Table Comment
	->setComment('Sagawa Slips Data Table');
$connection->createTable($table);

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