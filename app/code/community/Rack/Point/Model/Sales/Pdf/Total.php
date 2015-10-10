<?php

/**
 * Created by Celtic Corporation.
 * User: ndlinh
 * Date: Apr 6, 2012
 * Time: 2:37:10 PM
 */
class Rack_Point_Model_Sales_Pdf_Total extends Mage_Sales_Model_Order_Pdf_Total_Default
{
    /**
     * Get array of arrays with totals information for display in PDF
     * array(
     *  $index => array(
     *      'amount'   => $amount,
     *      'label'    => $label,
     *      'font_size'=> $font_size
     *  )
     * )
     * @return array
     */
    public function getTotalsForDisplay()
    {
        $pointAmount = $this->getOrder()->getPointUsed();
        $currency = Mage::helper('rackpoint')->point2Currency($pointAmount,$this->getOrder()->getPointRate());
        $currency = $this->getOrder()->getBaseCurrency()->formatTxt(-$currency);
        
        $amount = Mage::helper('rackpoint')->formatPoint(-$pointAmount) . '(' . $currency . ')';
        $label = Mage::helper('sales')->__($this->getTitle()) . ':';
        $fontSize = $this->getFontSize() ? $this->getFontSize() : 7;
        $total = array(
            'amount'    => $amount,
            'label'     => $label,
            'font_size' => $fontSize
        );
        return array($total);
    }

    public function canDisplay()
    {
        $pointAmount = $this->getOrder()->getPointUsed();
        return $this->getDisplayZero() || ($pointAmount != 0);
    }
}