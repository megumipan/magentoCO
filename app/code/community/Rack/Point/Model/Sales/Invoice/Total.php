<?php

class Rack_Point_Model_Sales_Invoice_Total extends Mage_Sales_Model_Order_Invoice_Total_Abstract
{
    public function collect(Mage_Sales_Model_Order_Invoice $invoice)
    {
        $order = $invoice->getOrder();
        if ($order->getPointUsed() > 0) {
            $remainUsedPoint = $order->getPointUsed() - $order->getPointUsedInvoiced() - $order->getPointUsedCancelled();
            if ($remainUsedPoint > 0) {
                if ($invoice->getBaseGrandTotal() <= $remainUsedPoint) {
                    $remainUsedPoint = ceil($invoice->getBaseGrandTotal());
                    $invoice->setBaseGrandTotal(0);
                    $invoice->setGrandTotal(0);
                } else {
                    $invoice->setBaseGrandTotal((float)$invoice->getBaseGrandTotal() - (float)$remainUsedPoint);
                    $invoice->setGrandTotal((float)$invoice->getGrandTotal() - (float)$remainUsedPoint);
                }
                $invoice->setPointUsed($remainUsedPoint);
            }
        }

        return $this;
    }

}