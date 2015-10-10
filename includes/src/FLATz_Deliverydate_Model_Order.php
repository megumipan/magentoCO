<?php

class FLATz_Deliverydate_Model_Order extends FLATz_Base_Model_Sales_Order
{
  public function getShippingDescription() {
    $html = parent::getShippingDescription();
    if ($this->getDeliveryDate() || $this->getTimeslot()) {
      $html .= Mage::helper('deliverydate')->__(' (Shipping Date: %s, Timeslot: %s)', $this->getDeliveryDate(), $this->getTimeslot());
    }
    return $html;
  }
}
