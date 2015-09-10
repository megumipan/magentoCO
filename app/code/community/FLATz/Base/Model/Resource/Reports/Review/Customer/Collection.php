<?php

/**
 * @license http://opensource.org/licenses/osl-3.0.php OSL-3.0
 * @author Yoshihisa AMAKATA <amakata@flatz.jp>
 * @copyright (c) 2013, FLATz Inc.
 */
class FLATz_Base_Model_Resource_Reports_Review_Customer_Collection extends Mage_Reports_Model_Resource_Review_Customer_Collection {

    /**
     * 
     * @return \FLATz_Base_Model_Resource_Reports_Review_Customer_Collection
     */
    public function joinCustomers() {
        if (!Mage::getStoreConfig('flatz_base_japanese/name/enablejp')) {
            return parent::joinCustomers();
        }

        /**
         * Allow to use analytic function to result select
         */
        $this->_useAnalyticFunction = true;

        /** @var $adapter Varien_Db_Adapter_Interface */
        $adapter = $this->getConnection();
        /** @var $customer Mage_Customer_Model_Resource_Customer */
        $customer = Mage::getResourceSingleton('customer/customer');
        /** @var $firstnameAttr Mage_Eav_Model_Entity_Attribute */
        $firstnameAttr = $customer->getAttribute('firstname');
        /** @var $lastnameAttr Mage_Eav_Model_Entity_Attribute */
        $lastnameAttr = $customer->getAttribute('lastname');

        $firstnameCondition = array('table_customer_firstname.entity_id = detail.customer_id');

        if ($firstnameAttr->getBackend()->isStatic()) {
            $firstnameField = 'firstname';
        } else {
            $firstnameField = 'value';
            $firstnameCondition[] = $adapter->quoteInto('table_customer_firstname.attribute_id = ?', (int) $firstnameAttr->getAttributeId());
        }

        $this->getSelect()->joinInner(
                array('table_customer_firstname' => $firstnameAttr->getBackend()->getTable()), implode(' AND ', $firstnameCondition), array());


        $lastnameCondition = array('table_customer_lastname.entity_id = detail.customer_id');
        if ($lastnameAttr->getBackend()->isStatic()) {
            $lastnameField = 'lastname';
        } else {
            $lastnameField = 'value';
            $lastnameCondition[] = $adapter->quoteInto('table_customer_lastname.attribute_id = ?', (int) $lastnameAttr->getAttributeId());
        }

        //Prepare fullname field result
        $customerFullname = $adapter->getConcatSql(array(
            "table_customer_lastname.{$lastnameField}",
            "table_customer_firstname.{$firstnameField}"
                ), ' ');
        $this->getSelect()->reset(Zend_Db_Select::COLUMNS)
                ->joinInner(
                        array('table_customer_lastname' => $lastnameAttr->getBackend()->getTable()), implode(' AND ', $lastnameCondition), array())
                ->columns(array(
                    'customer_name' => $customerFullname,
                    'review_cnt' => 'COUNT(main_table.review_id)'))
                ->group('detail.customer_id');

        return $this;
    }

}
