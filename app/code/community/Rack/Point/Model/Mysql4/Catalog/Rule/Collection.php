<?php

class Rack_Point_Model_Mysql4_Catalog_Rule_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    protected function _construct()
    {
        $this->_init('rackpoint/catalog_rule');
    }
    
    /**
     * Find product attribute in conditions or actions
     *
     * @param string $attributeCode
     * @return Rack_Point_Model_Mysql4_Catalog_Rule_Collection
     */
    public function addAttributeInConditionFilter($attributeCode)
    {
        $match = sprintf('%%%s%%', substr(serialize(array('attribute' => $attributeCode)), 5, -1));
        $this->addFieldToFilter('conditions_serialized', array('like' => $match));

        return $this;
    }
}