<?php

class Rack_Point_Model_Mysql4_Point_History_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    protected function _construct() 
    {
        $this->_init('rackpoint/point_history');
    }
    
    public function addCustomerInfo()
    {
        if ($this->getFlag('customer_added')) {
            return $this;
        }

        $customer = Mage::getModel('customer/customer');
        /* @var $customer Mage_Customer_Model_Customer */
        $firstname  = $customer->getAttribute('firstname');
        $lastname   = $customer->getAttribute('lastname');
        
        $connection = $this->getConnection();
        /* @var $connection Zend_Db_Adapter_Abstract */

        $this->getSelect()
            ->joinInner(
                array('ce' => $customer->getAttribute('email')->getBackend()->getTable()),
                'ce.entity_id=main_table.customer_id',
                array('customer_email' => 'email')
             )
            ->joinLeft(
                array('clt' => $lastname->getBackend()->getTable()),
                $connection->quoteInto('clt.entity_id=main_table.customer_id AND clt.attribute_id = ?', $lastname->getAttributeId()),
                array('customer_lastname' => 'value')
             )
             ->joinLeft(
                array('cft' => $firstname->getBackend()->getTable()),
                $connection->quoteInto('cft.entity_id=main_table.customer_id AND cft.attribute_id = ?', $firstname->getAttributeId()),
                array('customer_firstname' => 'value')
             );

        $this->setFlag('customer_added', true);
        return $this;
    }
}
