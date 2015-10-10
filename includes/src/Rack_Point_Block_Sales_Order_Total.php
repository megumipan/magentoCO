<?php

class Rack_Point_Block_Sales_Order_Total extends Mage_Core_Block_Template
{
    /**
     * Get label cell tag properties
     *
     * @return string
     */
    public function getLabelProperties()
    {
        return $this->getParentBlock()->getLabelProperties();
    }

    /**
     * Get order store object
     *
     * @return Mage_Sales_Model_Order
     */
    public function getOrder()
    {
        return $this->getParentBlock()->getOrder();
    }

    /**
     * Get totals source object
     *
     * @return Mage_Sales_Model_Order
     */
    public function getSource()
    {
        return $this->getParentBlock()->getSource();
    }

    /**
     * Get value cell tag properties
     *
     * @return string
     */
    public function getValueProperties()
    {
        return $this->getParentBlock()->getValueProperties();
    }

    /**
     * Initialize gift wrapping totals
     *
     * @return Rack_GiftWrapping_Block_Sales_Order_Total
     */
    public function initTotals()
    {
        /* @var $_helper Rack_Point_Helper_Data */
        $_helper = Mage::helper('rackpoint');
        $source = $this->getSource();
        if ($this->getOrder()->getPointReceived()) {
            $value  = $source->getPointReceived();
            if ($source instanceof Mage_Sales_Model_Order_Invoice) {
                $label = Mage::helper('rackpoint')->__('Received point for this invoice');
            } elseif ($source instanceof Mage_Sales_Model_Order_Creditmemo) {
                $label = Mage::helper('rackpoint')->__('Received point refunded');
            } else {
                $label = Mage::helper('rackpoint')->__('Received point for this order');
            }
            $this->getParentBlock()->addTotal(new Varien_Object(array(
                'code'   => 'point_received',
                'label'  => $label,
                'value'  => $this->__('%s points', number_format($value)),
                'is_formated' => true
            )), 'last');

            if ($source instanceof Mage_Sales_Model_Order) {
                if ($source->getPointReceivedInvoiced()) {
                    $this->getParentBlock()->addTotal(new Varien_Object(array(
                        'code'   => 'point_received_invoiced',
                        'label'  => $this->__('Received point invoiced'),
                        'value'  => $this->__('%s points', number_format($source->getPointReceivedInvoiced())),
                        'is_formated' => true
                    )), 'last');
                }
                if ($source->getPointReceivedRefunded()) {
                    $this->getParentBlock()->addTotal(new Varien_Object(array(
                        'code'   => 'point_received_refunded',
                        'label'  => $this->__('Received point refunded'),
                        'value'  => $this->__('%s points', number_format($source->getPointReceivedRefunded())),
                        'is_formated' => true
                    )), 'last');
                }
                if ($source->getPointReceivedCanceled()) {
                    $this->getParentBlock()->addTotal(new Varien_Object(array(
                        'code'   => 'point_received_refunded',
                        'label'  => $this->__('Received point cancelled'),
                        'value'  => $this->__('%s points', number_format($source->getPointReceivedCanceled())),
                        'is_formated' => true
                    )), 'last');
                }
            }
        }

        if ($this->getOrder()->getPointUsed()) {
            $value  = $source->getPointUsed();
            $order = $source;
            if ($source instanceof Mage_Sales_Model_Order_Invoice) {
                $label = Mage::helper('rackpoint')->__('Point Used');
                $order = $source->getOrder();
            } elseif ($source instanceof Mage_Sales_Model_Order_Creditmemo) {
                $label = Mage::helper('rackpoint')->__('Point Used Refunded');
                $order = $source->getOrder();
            } else {
                $label = Mage::helper('rackpoint')->__('Point Used');
            }
            $currency = Mage::helper('rackpoint')->point2Currency($value, $this->getOrder()->getPointRate());
            $currency = $_helper->convertCurrency($currency, $order->getOrderCurrencyCode());
            $this->getParentBlock()->addTotalBefore(new Varien_Object(array(
                'code'   => 'point_used',
                'label'  => $label,
                'value'  => $this->__('-%s points (-%s)', $value, $this->getOrder()->formatPrice($currency)),
                'is_formated' => true,
            )), 'grand_total');

            if ($source instanceof Mage_Sales_Model_Order) {
                if ($source->getPointUsedRefunded()) {
                    $this->getParentBlock()->addTotal(new Varien_Object(array(
                        'code'   => 'point_used_refunded',
                        'label'  => $this->__('Point Used Refunded'),
                        'value'  => $this->__('%s points', number_format($source->getPointUsedRefunded())),
                        'is_formated' => true
                    )), 'last');
                }
                if ($source->getPointUsedCanceled()) {
                    $this->getParentBlock()->addTotal(new Varien_Object(array(
                        'code'   => 'point_used_refunded',
                        'label'  => $this->__('Point Used Cancelled'),
                        'value'  => $this->__('%s points', number_format($source->getPointUsedCanceled())),
                        'is_formated' => true
                    )), 'last');
                }
            }
        }

        return $this;
    }
}
