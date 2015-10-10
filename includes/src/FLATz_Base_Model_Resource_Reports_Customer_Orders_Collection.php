<?php

/**
 * @license http://opensource.org/licenses/osl-3.0.php OSL-3.0
 * @author Yoshihisa AMAKATA <amakata@flatz.jp>
 * @copyright (c) 2013, FLATz Inc.
 */
class FLATz_Base_Model_Resource_Reports_Customer_Orders_Collection extends Mage_Reports_Model_Resource_Customer_Orders_Collection {

    /**
     * 姓名の順序を切り替える
     *
     * @param string $alias
     * @return \Mage_Reports_Model_Resource_Order_Collection
     */
    public function joinCustomerName($alias = 'name') {
        if (!Mage::getStoreConfig('flatz_base_japanese/name/enablejp')) {
            return parent::joinCustomerName($alias);
        }
        $fields = array('main_table.customer_lastname', 'main_table.customer_firstname');
        $fieldConcat = $this->getConnection()->getConcatSql($fields, ' ');
        $this->getSelect()->columns(array($alias => $fieldConcat));
        return $this;
    }

}
