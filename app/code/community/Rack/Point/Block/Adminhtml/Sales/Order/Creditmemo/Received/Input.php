<?php

class Rack_Point_Block_Adminhtml_Sales_Order_Creditmemo_Received_Input extends Mage_Adminhtml_Block_Template
{
    public function getCreditmetmo()
    {
        return Mage::registry('current_creditmemo');
    }
    
    public function getOrder()
    {
        return $this->getCreditmetmo()->getOrder();
    }

    public function getReceivedPointForCreditmemo()
    {
        $order = $this->getOrder();
        return ($order->getPointReceivedInvoiced() - $order->getPointReceivedRefunded());
    }
    
    public function canShow()
    {
        return ($this->getOrder()->getReceivePointMode() == Rack_Point_Model_Source_Mode::RECEIVE_MODE_ORDER);
    }
}