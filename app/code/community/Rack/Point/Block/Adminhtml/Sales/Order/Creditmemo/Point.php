<?php

class Rack_Point_Block_Adminhtml_Sales_Order_Creditmemo_Point extends Mage_Adminhtml_Block_Template
{
    /**
     * Getter
     *
     * @return Mage_Sales_Model_Order_Creditmemo
     */
    public function getCreditmemo()
    {
        return Mage::registry('current_creditmemo');
    }

    /**
     * Check whether can refund points to customer
     *
     * @return boolean
     */
    public function canRefundPoint()
    {
        if ($this->getCreditmemo()->getOrder()->getCustomerIsGuest()) {
            return false;
        }
        if ($this->getCreditmemo()->getPointUsed() <= 0) {
            return false;
        }
        
        return true;
    }

    /**
     * Return maximum points to refund
     *
     * @return integer
     */
    public function getUsedPoint()
    {
        return (int)$this->getCreditmemo()->getPointUsed();
    }
}
