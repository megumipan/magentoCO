<?php

class Rack_Point_Model_Mysql4_Rule_Product_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{

    public function _construct()
    {
        $this->_init('rackpoint/rule_product');
    }

    /**
     * Add product filter to collection
     *
     * @param   mixed $products
     * @return  Rack_Point_Model_Mysql4_Rule_Product_Collection
     */
    public function addProductsFilter($products)
    {
        $productIds = array();
        foreach ($products as $product) {
            if ($product instanceof Mage_Catalog_Model_Product) {
                $productIds[] = $product->getId();
            } else {
                $productIds[] = $product;
            }
        }
        if (empty($productIds)) {
            $productIds[] = false;
            $this->_setIsLoaded(true);
        }
        $this->addFieldToFilter('main_table.product_id', array('in' => $productIds));

        return $this;
    }

    /**
     * Add store filter to collection
     * 
     * @param int $websiteId 
     * @return Rack_Point_Model_Mysql4_Rule_Product_Collection
     */
    public function addWebsiteFilter($websiteId = null)
    {
        if ($websiteId == null) {
            $websiteId = Mage::app()->getWebsite()->getId();
        }

        $this->addFieldToFilter('main_table.website_id', $websiteId);

        return $this;
    }
    
    /**
     * Add customer group id to filter
     * 
     * @param int $groupId 
     * @return Rack_Point_Model_Mysql4_Rule_Product_Collection
     */
    public function addCustomerGroupFilter($groupId = null)
    {
        if ($groupId == null) {
            $customer = Mage::getSingleton('customer/session')->getCustomer();
            if ($customer->getId()) {
                $groupId = $customer->getGroupId();
            } else {
                $groupId = 0;
            }
        }
        
        $this->addFieldToFilter('main_table.customer_group_id', $groupId);
        
        return $this;
    }
    
    public function addExpiredFilter()
    {
        $this->addFieldToFilter('from_time', array(array('eq' => 0),array('lteq' => time()))); //川井さんの追加
        $this->addFieldToFilter('to_time', array(array('eq' => 0), array('gteq' => time())));
    
        return $this;
    }

}