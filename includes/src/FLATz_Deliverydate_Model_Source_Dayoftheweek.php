<?php
class FLATz_Deliverydate_Model_Source_Dayoftheweek {
  // ISO-8601 形式の、曜日の数値表現と同一に定義
  const SUNDAY = 7;
  const MONDAY = 1;
  const TUESDAY = 2;
  const WEDNESDAY = 3;
  const THURSDAY = 4;
  const FRIDAY = 5;
  const SATURDAY = 6;
  public function toOptionArray()
  {
    return array(
      array('value' => self::SUNDAY     , 'label' => Mage::helper('deliverydate')->__('Sunday')),
      array('value' => self::MONDAY     , 'label' => Mage::helper('deliverydate')->__('Monday')),
      array('value' => self::TUESDAY    , 'label' => Mage::helper('deliverydate')->__('Tuesday')),
      array('value' => self::WEDNESDAY  , 'label' => Mage::helper('deliverydate')->__('Wednesday')),
      array('value' => self::THURSDAY   , 'label' => Mage::helper('deliverydate')->__('Thursday')),
      array('value' => self::FRIDAY     , 'label' => Mage::helper('deliverydate')->__('Friday')),
      array('value' => self::SATURDAY   , 'label' => Mage::helper('deliverydate')->__('Saturday')),
    );
  }
}
