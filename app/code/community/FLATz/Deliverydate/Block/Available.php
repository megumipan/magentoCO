<?php

class FLATz_Deliverydate_Block_Available extends Mage_Checkout_Block_Onepage_Shipping_Method_Available {

  public function getDeliveryDate() {
    return $this->getQuote()->getDeliveryDate();
  }

  public function getTimeslotValue() {
    return $this->getQuote()->getTimeslot();
  }
}
