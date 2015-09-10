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
$table = $installer->getConnection()
    ->newTable($installer->getTable('yamato/slips'))
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
    ->addColumn('tracking_number', Varien_Db_Ddl_Table::TYPE_TEXT, 15, array(), 'Tracking Number')
	// Customer Data
    ->addColumn('customer_number', Varien_Db_Ddl_Table::TYPE_TEXT, 20, array(), 'Customer Number')
    ->addColumn('customer_id', Varien_Db_Ddl_Table::TYPE_TEXT, 20, array(), 'Customer ID')
    ->addColumn('customer_tel', Varien_Db_Ddl_Table::TYPE_TEXT, 15, array(), 'Customer Telephone')
    ->addColumn('customer_tel_branch_num', Varien_Db_Ddl_Table::TYPE_TEXT, 2, array(), 'Customer Telephone Branch Number')
    ->addColumn('customer_postcode', Varien_Db_Ddl_Table::TYPE_TEXT, 8, array(), 'Customer Post Code')
    ->addColumn('customer_address', Varien_Db_Ddl_Table::TYPE_TEXT, 64, array(), 'Customer Address')
    ->addColumn('customer_apart_name', Varien_Db_Ddl_Table::TYPE_TEXT, 32, array(), 'Customer Apartment Name')
    ->addColumn('customer_department1', Varien_Db_Ddl_Table::TYPE_TEXT, 50, array(), 'Department 1 of Customer')
    ->addColumn('customer_department2', Varien_Db_Ddl_Table::TYPE_TEXT, 50, array(), 'Department 2 of Customer')
    ->addColumn('customer_full_name', Varien_Db_Ddl_Table::TYPE_TEXT, 32, array(), 'Customer Full Name')
    ->addColumn('customer_full_name_kana', Varien_Db_Ddl_Table::TYPE_TEXT, 100, array(), 'Customer Full Name (kana)')
    ->addColumn('customer_prefix', Varien_Db_Ddl_Table::TYPE_TEXT, 4, array(), 'Customer Prefix')
	// Store Data
    ->addColumn('store_member_num', Varien_Db_Ddl_Table::TYPE_TEXT, 20, array(), 'Store Member Number')
    ->addColumn('store_tel', Varien_Db_Ddl_Table::TYPE_TEXT, 15, array(), 'Store Telephone')
    ->addColumn('store_tel_branch_num', Varien_Db_Ddl_Table::TYPE_TEXT, 2, array(), 'Store Telephone Branch Number')
    ->addColumn('store_postcode', Varien_Db_Ddl_Table::TYPE_TEXT, 8, array(), 'Store Post Code')
    ->addColumn('store_address', Varien_Db_Ddl_Table::TYPE_TEXT, 64, array(), 'Store Address')
    ->addColumn('store_apart_name', Varien_Db_Ddl_Table::TYPE_TEXT, 32, array(), 'Store Apartment Name')
    ->addColumn('store_name', Varien_Db_Ddl_Table::TYPE_TEXT, 32, array(), 'Store Name')
    ->addColumn('store_name_kana', Varien_Db_Ddl_Table::TYPE_TEXT, 100, array(), 'Store Name (kana)')
    // Delivery Statuses
    ->addColumn('delivary_mode', Varien_Db_Ddl_Table::TYPE_TEXT, 1, array(), 'Delivery Mode')
    ->addColumn('cool_type', Varien_Db_Ddl_Table::TYPE_TEXT, 1, array(), 'Cool Type')
    ->addColumn('slip_number', Varien_Db_Ddl_Table::TYPE_TEXT, 12, array(), 'Slip Number')
    ->addColumn('shipment_date', Varien_Db_Ddl_Table::TYPE_TEXT, 10, array(), 'Shipment Date')
    ->addColumn('delivery_date', Varien_Db_Ddl_Table::TYPE_TEXT, 10, array(), 'Delivery Date')
    ->addColumn('delivery_time', Varien_Db_Ddl_Table::TYPE_TEXT, 4, array(), 'Delivery Time')
    ->addColumn('product_id_1', Varien_Db_Ddl_Table::TYPE_TEXT, 30, array(), 'Product ID 1')
    ->addColumn('product_name_1', Varien_Db_Ddl_Table::TYPE_TEXT, 50, array(), 'Product Name 1')
    ->addColumn('product_id_2', Varien_Db_Ddl_Table::TYPE_TEXT, 30, array(), 'Product ID 2')
    ->addColumn('product_name_2', Varien_Db_Ddl_Table::TYPE_TEXT, 50, array(), 'Product Name 2')
    ->addColumn('handling_1', Varien_Db_Ddl_Table::TYPE_TEXT, 20, array(), 'Handling 1')
    ->addColumn('handling_2', Varien_Db_Ddl_Table::TYPE_TEXT, 20, array(), 'Handling 2')
    ->addColumn('comment', Varien_Db_Ddl_Table::TYPE_TEXT, 32, array(), 'Comment')
    ->addColumn('cod_amount', Varien_Db_Ddl_Table::TYPE_TEXT, 7, array(), 'Cash on Delivery Amount(including tax)')
    ->addColumn('tax_amount', Varien_Db_Ddl_Table::TYPE_TEXT, 7, array(), 'Amount of Tax')
    ->addColumn('held_yamato_office', Varien_Db_Ddl_Table::TYPE_TEXT, 1, array(), 'Held at Yamato Office')
    ->addColumn('yamato_office_id', Varien_Db_Ddl_Table::TYPE_TEXT, 6, array(), 'Yamato Office ID')
    ->addColumn('number_of_issued', Varien_Db_Ddl_Table::TYPE_TEXT, 2, array(), 'Number of Issued')
    ->addColumn('number_display_flag', Varien_Db_Ddl_Table::TYPE_TEXT, 1, array(), 'The Number Display Flag')
    // Order Status
    ->addColumn('invoice_customer_id', Varien_Db_Ddl_Table::TYPE_TEXT, 15, array(), 'Invoice Customer ID')
    ->addColumn('invoice_class_id', Varien_Db_Ddl_Table::TYPE_TEXT, 3, array(), 'Invoice Class ID')
    ->addColumn('shipping_charge_number', Varien_Db_Ddl_Table::TYPE_TEXT, 2, array(), 'Shipping Charge Number')
    ->addColumn('card_pay_entry', Varien_Db_Ddl_Table::TYPE_TEXT, 1, array(), 'Card Payment Entry')
    ->addColumn('card_pay_shop_number', Varien_Db_Ddl_Table::TYPE_TEXT, 9, array(), 'Card Payment Shop Number')
    ->addColumn('card_pay_acceptance_number1', Varien_Db_Ddl_Table::TYPE_TEXT, 23, array(), 'Card Payment Acceptance Number 1')
    ->addColumn('card_pay_acceptance_number2', Varien_Db_Ddl_Table::TYPE_TEXT, 23, array(), 'Card Payment Acceptance Number 2')
    ->addColumn('card_pay_acceptance_number3', Varien_Db_Ddl_Table::TYPE_TEXT, 23, array(), 'Card Payment Acceptance Number 3')
    ->addColumn('enable_email_notice_schedule', Varien_Db_Ddl_Table::TYPE_TEXT, 1, array(), 'Enable Email to Notice Schedule')
    ->addColumn('email_notice_schedule', Varien_Db_Ddl_Table::TYPE_TEXT, 60, array(), 'Email to Notice Schedule')
    ->addColumn('input_equipment', Varien_Db_Ddl_Table::TYPE_TEXT, 1, array(), 'Input Equipment')
    ->addColumn('email_message_notice_schedule', Varien_Db_Ddl_Table::TYPE_TEXT, 148, array(), 'Email Message to Notice Schedule')
    ->addColumn('enable_email_notice_complete', Varien_Db_Ddl_Table::TYPE_TEXT, 1, array(), 'Enable Email to Notice Complete')
    ->addColumn('email_notice_complete', Varien_Db_Ddl_Table::TYPE_TEXT, 60, array(), 'Email to Notice Complete')
    ->addColumn('email_message_notice_complete', Varien_Db_Ddl_Table::TYPE_TEXT, 318, array(), 'Email Message to Notice Complete')
     // Receiving Agent
    ->addColumn('rec_agent_flag', Varien_Db_Ddl_Table::TYPE_TEXT, 1, array(), 'Enable to Use Receiving Agent')
    ->addColumn('rec_agent_qr_code', Varien_Db_Ddl_Table::TYPE_TEXT, 1, array(), 'Receiving Agent QR code')
    ->addColumn('rec_agent_amount', Varien_Db_Ddl_Table::TYPE_TEXT, 7, array(), 'Receiving Agent Amount(including tax)')
    ->addColumn('rec_agent_amount_of_tax', Varien_Db_Ddl_Table::TYPE_TEXT, 7, array(), 'Receiving Agent Amount of Tax')
    ->addColumn('rec_agent_invoice_postcode', Varien_Db_Ddl_Table::TYPE_TEXT, 8, array(), 'Receiving Agent Invoice PostCode')
    ->addColumn('rec_agent_invoice_address', Varien_Db_Ddl_Table::TYPE_TEXT, 64, array(), 'Receiving Agent Invoice Address')
    ->addColumn('rec_agent_invoice_appat_name', Varien_Db_Ddl_Table::TYPE_TEXT, 32, array(), 'Receiving Agent Invoice Apartment Name')
    ->addColumn('rec_agent_department1', Varien_Db_Ddl_Table::TYPE_TEXT, 50, array(), 'Department 1 of Receiving Agent')
    ->addColumn('rec_agent_department2', Varien_Db_Ddl_Table::TYPE_TEXT, 50, array(), 'Department 2 of Receiving Agent')
    ->addColumn('rec_agent_invoice_name', Varien_Db_Ddl_Table::TYPE_TEXT, 32, array(), 'Receiving Agent Invoice Name')
    ->addColumn('rec_agent_invoice_name_kana', Varien_Db_Ddl_Table::TYPE_TEXT, 50, array(), 'Receiving Agent Invoice Name (kana)')
    ->addColumn('rec_agent_ref_name', Varien_Db_Ddl_Table::TYPE_TEXT, 32, array(), 'Receiving Agent Reference Name')
    ->addColumn('rec_agent_ref_postcode', Varien_Db_Ddl_Table::TYPE_TEXT, 8, array(), 'Receiving Agent Reference PostCode')
    ->addColumn('rec_agent_ref_address', Varien_Db_Ddl_Table::TYPE_TEXT, 64, array(), 'Receiving Agent Reference Address')
    ->addColumn('rec_agent_ref_apart_name', Varien_Db_Ddl_Table::TYPE_TEXT, 32, array(), 'Receiving Agent Reference Apartment Name')
    ->addColumn('rec_agent_tel_num', Varien_Db_Ddl_Table::TYPE_TEXT, 15, array(), 'Receiving Agent Reference Telephone Number')
    ->addColumn('rec_agent_number', Varien_Db_Ddl_Table::TYPE_TEXT, 20, array(), 'Receiving Agent Number')
    ->addColumn('rec_agent_product_name', Varien_Db_Ddl_Table::TYPE_TEXT, 50, array(), 'Receiving Agent Product Name')
    ->addColumn('rec_agent_comment', Varien_Db_Ddl_Table::TYPE_TEXT, 28, array(), 'Receiving Agent Comment')
    // Additional Table Indexes beside the primary
	->addIndex($installer->getIdxName('yamato/slips', array('order_id')),array('order_id'))
    ->addIndex($installer->getIdxName('yamato/slips', array('customer_id')),array('customer_id'))
    // Table Comment
	->setComment('Yamato Slips Data Table');
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