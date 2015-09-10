<?php
/*
 * Irvine Systems Shipping Japan Ymt
 *
 * NOTICE OF LICENSE
 * This source file is subject to the Revised BSD License which is bundled with
 * this package in the files LICENSE.txt and LICENSE.html.
 *
 * @category	Magento Shipping Extension
 * @package		IrvineSystems_Yamato
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
$installer->run("
DROP TABLE IF EXISTS {$this->getTable('yamato_slips')};
CREATE TABLE {$this->getTable('yamato_slips')} (
  `slip_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Account ID',
  `order_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'Order Number',
  `tracking_number` varchar(15) DEFAULT NULL COMMENT 'Tracking Number',
  `customer_number` varchar(20) DEFAULT NULL COMMENT 'Customer Number',
  `customer_id` varchar(20) DEFAULT NULL COMMENT 'Customer ID',
  `customer_tel` varchar(15) DEFAULT NULL COMMENT 'Customer Telephone',
  `customer_tel_branch_num` varchar(2) DEFAULT NULL COMMENT 'Customer Telephone Branch Number',
  `customer_postcode` varchar(8) DEFAULT NULL COMMENT 'Customer Post Code',
  `customer_address` varchar(64) DEFAULT NULL COMMENT 'Customer Address',
  `customer_apart_name` varchar(32) DEFAULT NULL COMMENT 'Customer Apartment Name',
  `customer_department1` varchar(50) DEFAULT NULL COMMENT 'Department 1 of Customer',
  `customer_department2` varchar(50) DEFAULT NULL COMMENT 'Department 2 of Customer',
  `customer_full_name` varchar(32) DEFAULT NULL COMMENT 'Customer Full Name',
  `customer_full_name_kana` varchar(100) DEFAULT NULL COMMENT 'Customer Full Name (kana)',
  `customer_prefix` varchar(4) DEFAULT NULL COMMENT 'Customer Prefix',
  `store_member_num` varchar(20) DEFAULT NULL COMMENT 'Store Member Number',
  `store_tel` varchar(15) DEFAULT NULL COMMENT 'Store Telephone',
  `store_tel_branch_num` varchar(2) DEFAULT NULL COMMENT 'Store Telephone Branch Number',
  `store_postcode` varchar(8) DEFAULT NULL COMMENT 'Store Post Code',
  `store_address` varchar(64) DEFAULT NULL COMMENT 'Store Address',
  `store_apart_name` varchar(32) DEFAULT NULL COMMENT 'Store Apartment Name',
  `store_name` varchar(32) DEFAULT NULL COMMENT 'Store Name',
  `store_name_kana` varchar(100) DEFAULT NULL COMMENT 'Store Name (kana)',
  `delivary_mode` varchar(1) DEFAULT NULL COMMENT 'Delivery Mode',
  `cool_type` varchar(1) DEFAULT NULL COMMENT 'Cool Type',
  `slip_number` varchar(12) DEFAULT NULL COMMENT 'Slip Number',
  `shipment_date` varchar(10) DEFAULT NULL COMMENT 'Shipment Date',
  `delivery_date` varchar(10) DEFAULT NULL COMMENT 'Delivery Date',
  `delivery_time` varchar(4) DEFAULT NULL COMMENT 'Delivery Time',
  `product_id_1` varchar(30) DEFAULT NULL COMMENT 'Product ID 1',
  `product_name_1` varchar(50) DEFAULT NULL COMMENT 'Product Name 1',
  `product_id_2` varchar(30) DEFAULT NULL COMMENT 'Product ID 2',
  `product_name_2` varchar(50) DEFAULT NULL COMMENT 'Product Name 2',
  `handling_1` varchar(20) DEFAULT NULL COMMENT 'Handling 1',
  `handling_2` varchar(20) DEFAULT NULL COMMENT 'Handling 2',
  `comment` varchar(32) DEFAULT NULL COMMENT 'Comment',
  `cod_amount` varchar(7) DEFAULT NULL COMMENT 'Cash on Delivery Amount(including tax)',
  `tax_amount` varchar(7) DEFAULT NULL COMMENT 'Amount of Tax',
  `held_yamato_office` varchar(1) DEFAULT NULL COMMENT 'Held at Yamato Office',
  `yamato_office_id` varchar(6) DEFAULT NULL COMMENT 'Yamato Office ID',
  `number_of_issued` varchar(2) DEFAULT NULL COMMENT 'Number of Issued',
  `number_display_flag` varchar(1) DEFAULT NULL COMMENT 'The Number Display Flag',
  `invoice_customer_id` varchar(15) DEFAULT NULL COMMENT 'Invoice Customer ID',
  `invoice_class_id` varchar(3) DEFAULT NULL COMMENT 'Invoice Class ID',
  `shipping_charge_number` varchar(2) DEFAULT NULL COMMENT 'Shipping Charge Number',
  `card_pay_entry` varchar(1) DEFAULT NULL COMMENT 'Card Payment Entry',
  `card_pay_shop_number` varchar(9) DEFAULT NULL COMMENT 'Card Payment Shop Number',
  `card_pay_acceptance_number1` varchar(23) DEFAULT NULL COMMENT 'Card Payment Acceptance Number 1',
  `card_pay_acceptance_number2` varchar(23) DEFAULT NULL COMMENT 'Card Payment Acceptance Number 2',
  `card_pay_acceptance_number3` varchar(23) DEFAULT NULL COMMENT 'Card Payment Acceptance Number 3',
  `enable_email_notice_schedule` varchar(1) DEFAULT NULL COMMENT 'Enable Email to Notice Schedule',
  `email_notice_schedule` varchar(60) DEFAULT NULL COMMENT 'Email to Notice Schedule',
  `input_equipment` varchar(1) DEFAULT NULL COMMENT 'Input Equipment',
  `email_message_notice_schedule` varchar(148) DEFAULT NULL COMMENT 'Email Message to Notice Schedule',
  `enable_email_notice_complete` varchar(1) DEFAULT NULL COMMENT 'Enable Email to Notice Complete',
  `email_notice_complete` varchar(60) DEFAULT NULL COMMENT 'Email to Notice Complete',
  `email_message_notice_complete` text COMMENT 'Email Message to Notice Complete',
  `rec_agent_flag` varchar(1) DEFAULT NULL COMMENT 'Enable to Use Receiving Agent',
  `rec_agent_qr_code` varchar(1) DEFAULT NULL COMMENT 'Receiving Agent QR code',
  `rec_agent_amount` varchar(7) DEFAULT NULL COMMENT 'Receiving Agent Amount(including tax)',
  `rec_agent_amount_of_tax` varchar(7) DEFAULT NULL COMMENT 'Receiving Agent Amount of Tax',
  `rec_agent_invoice_postcode` varchar(8) DEFAULT NULL COMMENT 'Receiving Agent Invoice PostCode',
  `rec_agent_invoice_address` varchar(64) DEFAULT NULL COMMENT 'Receiving Agent Invoice Address',
  `rec_agent_invoice_appat_name` varchar(32) DEFAULT NULL COMMENT 'Receiving Agent Invoice Apartment Name',
  `rec_agent_department1` varchar(50) DEFAULT NULL COMMENT 'Department 1 of Receiving Agent',
  `rec_agent_department2` varchar(50) DEFAULT NULL COMMENT 'Department 2 of Receiving Agent',
  `rec_agent_invoice_name` varchar(32) DEFAULT NULL COMMENT 'Receiving Agent Invoice Name',
  `rec_agent_invoice_name_kana` varchar(50) DEFAULT NULL COMMENT 'Receiving Agent Invoice Name (kana)',
  `rec_agent_ref_name` varchar(32) DEFAULT NULL COMMENT 'Receiving Agent Reference Name',
  `rec_agent_ref_postcode` varchar(8) DEFAULT NULL COMMENT 'Receiving Agent Reference PostCode',
  `rec_agent_ref_address` varchar(64) DEFAULT NULL COMMENT 'Receiving Agent Reference Address',
  `rec_agent_ref_apart_name` varchar(32) DEFAULT NULL COMMENT 'Receiving Agent Reference Apartment Name',
  `rec_agent_tel_num` varchar(15) DEFAULT NULL COMMENT 'Receiving Agent Reference Telephone Number',
  `rec_agent_number` varchar(20) DEFAULT NULL COMMENT 'Receiving Agent Number',
  `rec_agent_product_name` varchar(50) DEFAULT NULL COMMENT 'Receiving Agent Product Name',
  `rec_agent_comment` varchar(28) DEFAULT NULL COMMENT 'Receiving Agent Comment',
  PRIMARY KEY (`slip_id`),
  KEY `IDX_YAMATO_SLIPS_ORDER_ID` (`order_id`),
  KEY `IDX_YAMATO_SLIPS_CUSTOMER_ID` (`customer_id`))
  ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Yamato Slips Data Table'
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