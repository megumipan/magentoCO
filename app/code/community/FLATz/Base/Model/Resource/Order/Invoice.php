<?php

/**
 * @license http://opensource.org/licenses/osl-3.0.php OSL-3.0
 * @author Yoshihisa AMAKATA <amakata@flatz.jp>
 * @copyright (c) 2013, FLATz Inc.
 */
class FLATz_Base_Model_Resource_Order_Invoice extends Mage_Sales_Model_Resource_Order_Invoice {

    /**
     * 
     * @return \FLATz_Base_Model_Resource_Order_Invoice
     */
    protected function _initVirtualGridColumns() {
        if (!Mage::getStoreConfig('flatz_base_japanese/name/enablejp')) {
            return parent::_initVirtualGridColumns();
        }
        $adapter = $this->_getReadAdapter();
        $checkedFirstname = $adapter->getIfNullSql('{{table}}.firstname', $adapter->quote(''));
        $checkedLastname = $adapter->getIfNullSql('{{table}}.lastname', $adapter->quote(''));

        $this->addVirtualGridColumn(
                        'billing_name', 'sales/order_address', array('billing_address_id' => 'entity_id'), $adapter->getConcatSql(array($checkedLastname, $adapter->quote(' '), $checkedFirstname))
                )
                ->addVirtualGridColumn(
                        'order_increment_id', 'sales/order', array('order_id' => 'entity_id'), 'increment_id'
                )
                ->addVirtualGridColumn(
                        'order_created_at', 'sales/order', array('order_id' => 'entity_id'), 'created_at'
        );

        return $this;
    }

}