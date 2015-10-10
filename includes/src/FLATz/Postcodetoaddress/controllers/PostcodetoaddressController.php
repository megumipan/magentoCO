<?php
class FLATz_Postcodetoaddress_PostcodetoaddressController extends Mage_Core_Controller_Front_Action
{

  public function getAction() {
    $url = Mage::getStoreConfig('postcodetoaddress/settings/request_url');
    $postcode = preg_replace('/[^0-9]/', '', $this->getRequest()->getParam('zip'));
    $country = $this->getRequest()->getParam('country');
    if (strtoupper($country) != 'JP') {
      echo json_encode(array('error' => 'only Japanese'));
      return;
    }
    $client = new Varien_Http_Client(sprintf($url, $postcode));
    $client->setMethod(Varien_Http_Client::GET);
    $response = $client->request();
    if ($response->isSuccessful()) {
      $addresses = json_decode($response->getBody(), true);
      if (!is_null($addresses)) {
        foreach ($addresses as $index => $address) {
          $res_address['pref'] = $address['pref'];
          $res_address['city'] = $address['city'];
          if ($index == 0) {
            $res_address['street'] = $address['address'];
          } else {
            $res_address['street'] = $this->_getIntersect($res_address['street'], $address['address']);
          }
        }
      } else {
        $res_address['error'] = Mage::helper('postcodetoaddress')->__('Address not found');
      }
    } else {
      $res_address['error'] = Mage::helper('postcodetoaddress')->__('System error');
    }
    echo json_encode($res_address);
  }

  protected function _getIntersect($a, $b) {
    for($i = 0 ; $i < strlen($a); ++$i) {
      if (substr($b, $i, 1) == substr($a, $i, 1)) {
        $res_str .= substr($b, $i, 1);
      } else {
        break;
      }
    }
    return $res_str;
  }


}
