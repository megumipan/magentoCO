<?php

/**
 * @license http://opensource.org/licenses/osl-3.0.php OSL-3.0
 * @author Yoshihisa AMAKATA <amakata@flatz.jp>
 * @copyright (c) 2013, FLATz Inc.
 */
class FLATz_Base_Model_Mysql4_Sales_Order_Creditmemo extends Mage_Sales_Model_Mysql4_Order_Creditmemo {

    /**
     * 
     * @return \FLATz_Base_Model_Mysql4_Sales_Order_Creditmemo
     */
    protected function _initVirtualGridColumns() {
        if (!Mage::getStoreConfig('flatz_base_japanese/name/enablejp')) {
            return parent::_initVirtualGridColumns();
        }
        parent::_initVirtualGridColumns();
        $this->addVirtualGridColumn(
                        'billing_name', 'sales/order_address', array('billing_address_id' => 'entity_id'), 'CONCAT(IFNULL({{table}}.lastname, ""), " ", IFNULL({{table}}.firstname, ""))'
                )
                ->addVirtualGridColumn(
                        'order_increment_id', 'sales/order', array('order_id' => 'entity_id'), 'increment_id'
                )
                ->addVirtualGridColumn(
                        'order_created_at', 'sales/order', array('order_id' => 'entity_id'), 'created_at'
                )
        ;

        return $this;
    }

}
