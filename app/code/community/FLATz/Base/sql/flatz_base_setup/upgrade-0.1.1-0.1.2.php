<?php
/**
 * @license http://opensource.org/licenses/osl-3.0.php OSL-3.0
 * @author Yoshihisa AMAKATA <amakata@flatz.jp>
 * @copyright (c) 2013, FLATz Inc.
 */
$installer = $this;

$installer->startSetup();

$table = $installer->getTable('customer/eav_attribute');

$id = $installer->getAttribute('customer', 'firstname', 'attribute_id');
$installer->run('update ' . $table . ' set sort_order=50 where attribute_id=' . $id);

$id = $installer->getAttribute('customer', 'lastname', 'attribute_id');
$installer->run('update ' . $table . ' set sort_order=40 where attribute_id=' . $id);

$id = $installer->getAttribute('customer_address', 'firstname', 'attribute_id');
$installer->run('update ' . $table . ' set sort_order=20 where attribute_id=' . $id);

$id = $installer->getAttribute('customer_address', 'lastname', 'attribute_id');
$installer->run('update ' . $table . ' set sort_order=10 where attribute_id=' . $id);

$id = $installer->getAttribute('customer', 'firstnamekana', 'attribute_id');
$installer->run('update ' . $table . ' set sort_order=51 where attribute_id=' . $id);

$id = $installer->getAttribute('customer', 'lastnamekana', 'attribute_id');
$installer->run('update ' . $table . ' set sort_order=40 where attribute_id=' . $id);

$id = $installer->getAttribute('customer_address', 'firstnamekana', 'attribute_id');
$installer->run('update ' . $table . ' set sort_order=21 where attribute_id=' . $id);

$id = $installer->getAttribute('customer_address', 'lastnamekana', 'attribute_id');
$installer->run('update ' . $table . ' set sort_order=11 where attribute_id=' . $id);

$id = $installer->getAttribute('customer', 'middlename', 'attribute_id');
$installer->run('update ' . $table . ' set sort_order=120 where attribute_id=' . $id);

$id = $installer->getAttribute('customer', 'prefix', 'attribute_id');
$installer->run('update ' . $table . ' set sort_order=121 where attribute_id=' . $id);

$id = $installer->getAttribute('customer_address', 'middlename', 'attribute_id');
$installer->run('update ' . $table . ' set sort_order=122 where attribute_id=' . $id);

$id = $installer->getAttribute('customer_address', 'prefix', 'attribute_id');
$installer->run('update ' . $table . ' set sort_order=123 where attribute_id=' . $id);

$installer->endSetup();