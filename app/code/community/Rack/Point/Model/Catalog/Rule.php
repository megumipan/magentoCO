<?php

class Rack_Point_Model_Catalog_Rule extends Mage_Rule_Model_Rule
{
    const PERCENT = 'by_percent';
    const FIXED   = 'by_fixed';
    
    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'point_rule';

    /**
     * Parameter name in event
     *
     * In observe method you can use $observer->getEvent()->getRule() in this case
     *
     * @var string
     */
    protected $_eventObject = 'rule';
    
    /**
     * Matched product ids array
     *
     * @var array
     */
    protected $_productIds;
    
    protected function _construct()
    {
        parent::_construct();
        $this->_init('rackpoint/catalog_rule');
        $this->setIdFieldName('rule_id');
    }
    
    public function getConditionsInstance()
    {
        return Mage::getModel('catalogrule/rule_condition_combine');
    }

    public function getActionsInstance()
    {
        return Mage::getModel('catalogrule/rule_action_collection');
    }
    
    /**
     * Process rule related data after rule save
     *
     * @return Mage_CatalogRule_Model_Rule
     */
    protected function _afterSave()
    {
        $this->_getResource()->updateRuleProductData($this);
        parent::_afterSave();
    }

    /**
     * Get array of product ids which are matched by rule
     *
     * @return array
     */
    public function getMatchingProductIds()
    {
        if (is_null($this->_productIds)) {
            $this->_productIds = array();
            $this->setCollectedAttributes(array());
            $websiteIds = $this->getWebsiteIds();
            if (!is_array($websiteIds)) {
                $websiteIds = explode(',', $websiteIds);
            }
            if ($websiteIds) {
                $productCollection = Mage::getResourceModel('catalog/product_collection');

                $productCollection->addWebsiteFilter($websiteIds);
                $this->getConditions()->collectValidatedAttributes($productCollection);

                Mage::getSingleton('core/resource_iterator')->walk(
                    $productCollection->getSelect(),
                    array(array($this, 'callbackValidateProduct')),
                    array(
                        'attributes' => $this->getCollectedAttributes(),
                        'product'    => Mage::getModel('catalog/product'),
                    )
                );
            }
        }

        return $this->_productIds;
    }
    
    /**
     * Callback function for product matching
     *
     * @param $args
     * @return void
     */
    public function callbackValidateProduct($args)
    {
        $product = clone $args['product'];
        $product->setData($args['row']);

        if ($this->getConditions()->validate($product)) {
            $this->_productIds[] = $product->getId();
        }
    }
    
    /**
     * Get array of assigned customer group ids
     *
     * @return array
     */
    public function getCustomerGroupIds()
    {
        $ids = $this->getData('customer_group_ids');
        if (($ids && !$this->getCustomerGroupChecked()) || is_string($ids)) {
            if (is_string($ids)) {
                $ids = explode(',', $ids);
            }

            $groupIds = Mage::getModel('customer/group')->getCollection()->getAllIds();
            $ids = array_intersect($ids, $groupIds);
            $this->setData('customer_group_ids', $ids);
            $this->setCustomerGroupChecked(true);
        }
        return $ids;
    }
    
    /**
     * Apply all rules
     */
    public function applyAll()
    {
        $rules = $this->getCollection();
        foreach ($rules as $rule) {
            $rule->afterLoad();
            $this->_getResource()->updateRuleProductData($rule);
        }
    }
    
    /**
     * Apply rule for specific products
     * 
     * @param type $productId 
     */
    public function applyForProduct($product)
    {
        if (is_numeric($product)) {
            $product = Mage::getModel('catalog/product')->load($product);
        }
        if ($product->getId()) {
            $this->_getResource()->deleteRuleForProduct($product);
            $rules = $this->getCollection();
            foreach ($rules as $rule) {
                $rule->afterLoad();
                if($rule->getConditions()->validate($product)){
                    $this->_getResource()->updateRuleForProduct($rule, $product);
                }
            }

        }
    }
    
}