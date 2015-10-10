<?php

class Rack_Point_Model_Sales_Creditmemo_Total extends Mage_Sales_Model_Order_Creditmemo_Total_Abstract
{
    public function collect(Mage_Sales_Model_Order_Creditmemo $creditmemo)
    {
        $order = $creditmemo->getOrder();

        if ($order->getPointUsed() > 0) {
            $remainUsedPoint = $order->getPointUsed() - $order->getPointUsedRefunded() - $order->getPointUsedCanceled();
            if ($remainUsedPoint > 0) {
                if ($creditmemo->getBaseGrandTotal() <= $remainUsedPoint) {
                    $creditmemo->setBaseGrandTotal(0);
                    $creditmemo->setGrandTotal(0);
                    $creditmemo->setAllowZeroGrandTotal(true);
                } else {
                    $creditmemo->setBaseGrandTotal($creditmemo->getBaseGrandTotal() - $remainUsedPoint);
                    $creditmemo->setGrandTotal($creditmemo->getGrandTotal() - $remainUsedPoint);
                }
                $creditmemo->setPointUsed($remainUsedPoint);
            }
        }
        if ($order->getPointReceivedInvoiced() > 0) {
            $remainReceivedPoint = $order->getPointReceivedInvoiced() -  $order->getPointReceivedRefunded() -  $order->getPointReceivedCanceled();
            if ($remainReceivedPoint > 0 && $creditmemo->getGrandTotal() == 0) {
                $creditmemo->setAllowZeroGrandTotal(true);
            }
        }
        if ($order->getPointUsedRefunded() >= $order->getGrandTotal()) {
            $creditmemo->setBaseGrandTotal(0);
            $creditmemo->setGrandTotal(0);
            $creditmemo->setAllowZeroGrandTotal(true);
        }
        
        return $this;
    }
}