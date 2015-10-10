<?php

class FLATz_Deliverydate_Model_Observer extends Varien_Object {
  
  public function setDeliverydateToQuote(Varien_Event_Observer $ob) {
    if (Mage::getStoreConfig('deliverydate/settings_enable')) {
      $request = $ob->getEvent()->getRequest();
      $quote = $ob->getEvent()->getQuote();
      
      $shippingMethod = $request->getParam('shipping_method');
      $deliveryDate = $request->getParam('shipping_date_' . $shippingMethod);
      $timeslot = $request->getParam('shipping_timeslot_' . $shippingMethod);

      $quote->setDeliveryDate($deliveryDate);
      $quote->setTimeslot($timeslot);

      $quote->save();
    }

    return $this;
  }

  public function convertQuoteToOrder($observer) {
    if (Mage::getStoreConfig('deliverydate/settings_enable')) {
      $quote = $observer->getEvent()->getQuote();
      $order = $observer->getEvent()->getOrder();

      $settings = Mage::helper('deliverydate')->getShippableDates($quote->getShippingAddress()->getShippingMethod(), $quote->getStoreId());
      $order->setDeliveryDate($quote->getDeliveryDate() ? $quote->getDeliveryDate() : Mage::helper('deliverydate')->__('not specified'));
      $order->setTimeslot($settings['timeslot'][$quote->getTimeslot()]);
    }
    return $this;
  }

  public function setDeliverydateForOrderCreate($observer) {
    $request = $observer->getEvent()->getRequest();
    $quote   = $observer->getEvent()->getOrderCreateModel()->getQuote();
    
    $quote->setDeliveryDate($request['order']['delivery_date']);
    $quote->setTimeslot($request['order']['timeslot']);

    Mage::log($quote->getDeliveryDate());
    Mage::log($quote->getTimeslot());

    $quote->save();

    return $this;
  }
}
