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
// Keep Database Table Creation in MYSQL Code for Backward compatibility
$installer->run("
DROP TABLE IF EXISTS {$this->getTable('japanpost_slips')};
CREATE TABLE {$this->getTable('japanpost_slips')} (
  `slip_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Account ID',
  `order_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'Order Number',
  `tracking_number` varchar(15) DEFAULT NULL COMMENT 'Tracking Number',
  `customer_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'Customer ID',
  `customer_prefix` smallint(6) DEFAULT '9' COMMENT 'Customer Prefix',
  `customer_name` varchar(72) DEFAULT NULL COMMENT 'Customer Full Name',
  `customer_namekana` varchar(80) DEFAULT NULL COMMENT 'Customer Full Name (Kana)',
  `customer_address` varchar(120) DEFAULT NULL COMMENT 'Customer Full Address',
  `customer_postcode` varchar(10) DEFAULT NULL COMMENT 'Customer Post Code',
  `customer_tel` varchar(50) DEFAULT NULL COMMENT 'Customer Telephone Number',
  `customer_email` varchar(255) DEFAULT NULL COMMENT 'Customer Email',
  `notification_post` smallint(6) NOT NULL DEFAULT '0' COMMENT 'Post Notification',
  `notification_email` smallint(6) NOT NULL DEFAULT '0' COMMENT 'Email Notification',
  `store_memberid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'Store Member ID',
  `store_prefix` smallint(6) NOT NULL DEFAULT '9' COMMENT 'Store Prefix',
  `store_name` varchar(72) DEFAULT NULL COMMENT 'Store Full Name',
  `store_namekana` varchar(80) DEFAULT NULL COMMENT 'Store Full Name (Kana)',
  `store_address` varchar(120) DEFAULT NULL COMMENT 'Store Full Address',
  `store_postcode` varchar(10) DEFAULT NULL COMMENT 'Store Post Code',
  `store_tel` varchar(50) DEFAULT NULL COMMENT 'Store Telephone Number',
  `store_email` varchar(255) DEFAULT NULL COMMENT 'Store Email',
  `fragile_status` smallint(6) NOT NULL DEFAULT '0' COMMENT 'Fragile Shipping',
  `creature_status` smallint(6) NOT NULL DEFAULT '0' COMMENT 'Creature Shipping',
  `glass_status` smallint(6) NOT NULL DEFAULT '0' COMMENT 'Glass Shipping',
  `side_status` smallint(6) NOT NULL DEFAULT '0' COMMENT 'Side Shipping',
  `weight_status` smallint(6) NOT NULL DEFAULT '0' COMMENT 'Weight Shipping',
  `ship_cooler` smallint(6) NOT NULL DEFAULT '0' COMMENT 'Cooling Shipment',
  `package_weight` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'Package Weight',
  `package_size` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'Package Size',
  `product_number` varchar(255) DEFAULT NULL COMMENT 'Product Number',
  `product_name` varchar(255) DEFAULT NULL COMMENT 'Product Name',
  `delivery_mode` smallint(6) NOT NULL DEFAULT '0' COMMENT 'Delivery Mode',
  `shipping_date` date DEFAULT NULL COMMENT 'Extimate Shipping Date',
  `delivery_date` date DEFAULT NULL COMMENT 'Delivery Date',
  `delivery_time` varchar(15) DEFAULT NULL COMMENT 'Delivery Time',
  `delivery_type` smallint(6) NOT NULL DEFAULT '1' COMMENT 'Delivery Type',
  `delivery_comment` varchar(60) DEFAULT NULL COMMENT 'Discount Comment',
  `free_field2` varchar(60) DEFAULT NULL COMMENT 'Free Field 2',
  `free_field3` varchar(60) DEFAULT NULL COMMENT 'Free Field 3',
  `free_field4` varchar(60) DEFAULT NULL COMMENT 'Free Field 4',
  `free_field5` varchar(60) DEFAULT NULL COMMENT 'Free Field 5',
  `cod_status` smallint(6) NOT NULL DEFAULT '0' COMMENT 'Cash on Delivery Status',
  `cod_amount` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'Cash on Delivery Amount',
  `payment_source` smallint(6) NOT NULL DEFAULT '0' COMMENT 'Payment Source',
  `mail_class` smallint(6) NOT NULL DEFAULT '9' COMMENT 'Mail Class',
  `ship_service` smallint(6) NOT NULL DEFAULT '0' COMMENT 'Shipping Extra Service',
  `ensured_amount` int(11) DEFAULT NULL COMMENT 'Ensured Amount',
  `discount_type` smallint(6) NOT NULL DEFAULT '0' COMMENT 'Discount Type',
  `taxable` smallint(6) NOT NULL DEFAULT '1' COMMENT 'Taxable Shipment',
  `sort_code` int(11) DEFAULT NULL COMMENT 'Sort Code',
  PRIMARY KEY (`slip_id`),
  KEY `IDX_JAPANPOST_SLIPS_ORDER_ID` (`order_id`),
  KEY `IDX_JAPANPOST_SLIPS_CUSTOMER_ID` (`customer_id`))
  ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8 COMMENT='Japan Post Slips Data Table'
");

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