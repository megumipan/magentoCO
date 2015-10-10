<?php

/**
 * @license http://opensource.org/licenses/osl-3.0.php OSL-3.0
 * @author Yoshihisa AMAKATA <amakata@flatz.jp>
 * @copyright (c) 2013, FLATz Inc.
 */
class FLATz_Base_Model_Resource_Order_Grid_Collection extends Mage_Sales_Model_Resource_Order_Grid_Collection {

    /**
     * 
     * @return \FLATz_Base_Model_Resource_Order_Grid_Collection
     */
    protected function _initVirtualGridColumns() {
        if (!Mage::getStoreConfig('flatz_base_japanese/name/enablejp')) {
            return parent::_initVirtualGridColumns();
        }
        $this->_virtualGridColumns = array();
        if ($this->_eventPrefix && $this->_eventObject) {
            Mage::dispatchEvent($this->_eventPrefix . '_init_virtual_grid_columns', array(
                $this->_eventObject => $this
            ));
        }
        $adapter = $this->getReadConnection();
        $ifnullFirst = $adapter->getIfNullSql('{{table}}.firstname', $adapter->quote(''));
        $ifnullLast = $adapter->getIfNullSql('{{table}}.lastname', $adapter->quote(''));
        $concatAddress = $adapter->getConcatSql(array($ifnullLast, $adapter->quote(' '), $ifnullFirst));

        $this->addVirtualGridColumn(
                        'billing_name', 'sales/order_address', array('billing_address_id' => 'entity_id'), $concatAddress
            )
            ->addVirtualGridColumn(
                        'shipping_name', 'sales/order_address', array('shipping_address_id' => 'entity_id'), $concatAddress
            );

        return $this;
    }

}
    