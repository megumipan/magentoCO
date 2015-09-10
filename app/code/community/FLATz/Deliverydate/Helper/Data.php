<?php
class FLATz_Deliverydate_Helper_Data extends Mage_Sales_Helper_Data {

  public function getShippableDates($module, $storeId=null) {
    $datelist = json_decode(Mage::getStoreConfig('deliverydate/settings/datelist', $storeId), true);
    $options = null;
    if (isset($datelist[$module])) {
      $timeslot = $datelist[$module][0];
      $latency = (int)$datelist[$module][1];
      $during = (int)$datelist[$module][2];
      $shippableDays = explode(',', Mage::getStoreConfig('deliverydate/settings/shippable_days', $storeId));
      $holidays = preg_split("/\r?\n/", Mage::getStoreConfig('deliverydate/settings/holidays', $storeId));
      $i = 0;
      $add = 0;
      if (!empty($shippableDays)) {
        while (true) {
          if (in_array(date('N', time()+($i*86400)), $shippableDays) && !in_array(date('Y-m-d', time()+($i*86400)), $holidays)) {
            ++$add;
            if ($add > $latency && $add <= $during + $latency) {
              $dates[date('Y-m-d', time()+($i*86400))] = date('Y-m-d', time()+($i*86400));
            } elseif ($add >= $during + $latency) {
              break;
            } 
          }
          ++$i;
        }
      }
      $options['dates'] = array_merge(array(Mage::getStoreConfig('deliverydate/settings/default_date', $storeId) => Mage::getStoreConfig('deliverydate/settings/default_date', $storeId)), $dates);
      $options['timeslot'] = $timeslot;
    }
    return $options;
  }

  public function getSpecifiedDeliveryDate() {
    if (Mage::getStoreConfig('deliverydate/settings_enable')) {
      $address = Mage::getSingleton('checkout/session')->getQuote()->getShippingAddress();
      $settings = $this->getShippableDates($address->getShippingMethod());
      $date = $address->getQuote()->getDeliveryDate();
      $timeslot = $settings['timeslot'][$address->getQuote()->getTimeslot()];
      return $this->__('Shipping Date: %s <br /> Timeslot: %s', $date, $timeslot);
    }
  }
}
?>
