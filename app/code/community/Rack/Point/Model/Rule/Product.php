<?php

class Rack_Point_Model_Rule_Product extends Mage_Core_Model_Abstract
{
    protected function _construct()
    {
        $this->_init('rackpoint/rule_product');
    }
    
    /**
     * Load Rule by product id
     * 
     * @param int $id
     * @param int $websiteId
     * @return Rack_Point_Model_Catalog_Rule 
     */
    public function loadByProductId($id, $websiteId = null)
    {
        if (null == $websiteId) {
            $websiteId = Mage::app()->getWebsite()->getId();
        }
        $customer = Mage::getSingleton('customer/session')->getCustomer();
        $groupId = 0;
        if ($customer->getId()) {
            $groupId = $customer->getGroupId();
        }
        $collection = $this->getCollection();
        $collection->addFieldToSelect('main_table.product_id', $id);
        $collection->addFieldToSelect('main_table.website_id', $websiteId);
        $collection->addFieldToSelect('main_table.customer_group_id', $groupId);
        $collection->load();
        
        return $collection->getFirstItem();
    }
}