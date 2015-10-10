<?php
class FLATz_Base_Block_Adminhtml_Sales_Order_View_Info extends Mage_Adminhtml_Block_Sales_Order_View_Info {

    public function getCustomerAccountData()
    {
        $accountData = array();

        /* @var $config Mage_Eav_Model_Config */
        $config     = Mage::getSingleton('eav/config');
        $entityType = 'customer';
        $customer   = Mage::getModel('customer/customer');
        foreach ($config->getEntityAttributeCodes($entityType) as $attributeCode) {
            /* @var $attribute Mage_Customer_Model_Attribute */
            $attribute = $config->getAttribute($entityType, $attributeCode);
            if (!$attribute->getIsVisible() || $attribute->getIsSystem()) {
                continue;
            }
            $orderKey   = sprintf('customer_%s', $attribute->getAttributeCode());
            $orderValue = $this->getOrder()->getData($orderKey);
            if ($orderValue != '') {
                $customer->setData($attribute->getAttributeCode(), $orderValue);
                $dataModel  = Mage_Customer_Model_Attribute_Data::factory($attribute, $customer);
                $value      = $dataModel->outputValue(Mage_Customer_Model_Attribute_Data::OUTPUT_FORMAT_HTML);
                //fix bug
                $sortOrder  = $attribute->getSortOrder() + ($attribute->getIsUserDefined() ? 200 : 0);

                $sortOrder  = $this->_prepareAccountDataSortOrder($accountData, $sortOrder);
                $accountData[$sortOrder] = array(
                    'label' => $attribute->getFrontendLabel(),
                    'value' => $this->escapeHtml($value, array('br'))
                );
            }
        }

        ksort($accountData, SORT_NUMERIC);

        return $accountData;
    }
}