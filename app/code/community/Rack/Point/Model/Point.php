<?php

class Rack_Point_Model_Point
{
    public function calculatePoint($product, $amount, $type)
    {
        $value = 0;
        if ($type == Rack_Point_Model_Catalog_Rule::PERCENT) {
            $calculation = Mage::getStoreConfig('rackpoint/calculate/prod_includes_tax') == '1' ? true : false;
            $price = Mage::helper('tax')->getPrice($product, $product->getFinalPrice(), $calculation);
            $value = Mage::helper('rackpoint')->roundPoint($price * ($amount/100));
        } elseif ($type == Rack_Point_Model_Catalog_Rule::FIXED) {
            $value = $amount;
        }

        return $value;
    }

    public function addPointToProduct($product)
    {
        if (!($product instanceof Mage_Catalog_Model_Product)) {
            return;
        }
        $ruleProductCollection = Mage::getResourceModel('rackpoint/rule_product_collection');
        $ruleProductCollection->addWebsiteFilter()
            ->addCustomerGroupFilter()
            ->addFieldToFilter('product_id', $product->getId())
            ->addExpiredFilter()
            ->getSelect()->order('sort_order ASC')
            ->order('rule_id DESC');

        $pointData = array();
        foreach ($ruleProductCollection as $ruleProduct) {
            $type = $ruleProduct->getActionOperator();
            if (isset($pointData[$type])) {
                $pointData[$type]['amount'] += $ruleProduct->getActionAmount();
            } else {
                $pointData[$type] = array('amount' => $ruleProduct->getActionAmount(), 'type' => $type);
            }

            if ($ruleProduct->getActionStop() == 1) {
                break;
            }
        }
        if (empty($pointData)) {
            return;
        }
        $pointValue = 0;
        foreach ($pointData as $type => $data) {
            $pointValue += $this->calculatePoint($product, $data['amount'], $type);
        }
        $product->setRackPointValue($pointValue);
        $product->setPointDetails($pointData);
    }

    public function addPointToCollection($productCollection)
    {
        $ruleProductCollection = Mage::getResourceModel('rackpoint/rule_product_collection');
        $websiteId = null;
        $customeGroupId = null;
        if (Mage::app()->getStore()->isAdmin()) {
            $quote = Mage::getSingleton('adminhtml/session_quote');
            $websiteId = $quote->getStore()->getWebsiteId();
            $customeGroupId = $quote->getCustomer()->getGroupId();
        }
        $ruleProductCollection->addProductsFilter($productCollection)
            ->addWebsiteFilter($websiteId)
            ->addExpiredFilter()
            ->addCustomerGroupFilter($customeGroupId)
            ->getSelect()->order('sort_order ASC')
            ->order('rule_id DESC');

        $pointData = array();
        $stopData = array();
        foreach ($ruleProductCollection as $ruleProduct) {
            $pid = $ruleProduct->getProductId();
            if (isset($stopData[$pid]) && $stopData[$pid] == 1) {
                continue;
            }

            $stopData[$pid] = $ruleProduct->getActionStop();
            $type = $ruleProduct->getActionOperator();
            if (isset($pointData[$pid][$type])) {
                $pointData[$pid][$type]['amount'] += $ruleProduct->getActionAmount();
            } else {
                $pointData[$pid][$type] = array('amount' => $ruleProduct->getActionAmount(), 'type' => $type);
            }
        }
        unset($ruleProductCollection);
        $pointValue = 0;
        foreach ($productCollection as $prod) {
            if (isset($pointData[$prod->getId()])) {
                foreach ($pointData[$prod->getId()] as $type => $data) {
                    $pointValue += $this->calculatePoint($prod, $data['amount'], $type);
                }
                $prod->setRackPointValue($pointValue);
                $prod->setPointDetails($pointData[$prod->getId()]);
            }
        }
    }
}