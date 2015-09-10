<?php
/**
 * @license http://opensource.org/licenses/osl-3.0.php OSL-3.0
 * @author Yoshihisa AMAKATA <amakata@flatz.jp>
 * @copyright (c) 2013, FLATz Inc.
 */

$installer = $this;

$installer->startSetup();

$table = $installer->getTable('eav/attribute');
$id = $installer->getAttribute('customer', 'firstnamekana', 'attribute_id');
$installer->run('update ' . $table . ' set is_required=0 where attribute_id=' . $id);

$id = $installer->getAttribute('customer', 'lastnamekana', 'attribute_id');
$installer->run('update ' . $table . ' set is_required=0 where attribute_id=' . $id);

$id = $installer->getAttribute('customer_address', 'firstnamekana', 'attribute_id');
$installer->run('update ' . $table . ' set is_required=0 where attribute_id=' . $id);

$id = $installer->getAttribute('customer_address', 'lastnamekana', 'attribute_id');
$installer->run('update ' . $table . ' set is_required=0 where attribute_id=' . $id);

$installer->endSetup();