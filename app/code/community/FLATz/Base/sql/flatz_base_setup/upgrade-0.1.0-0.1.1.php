<?php
/**
 * @license http://opensource.org/licenses/osl-3.0.php OSL-3.0
 * @author Yoshihisa AMAKATA <amakata@flatz.jp>
 * @copyright (c) 2013, FLATz Inc.
 */

$installer = $this;

$eavConfig = Mage::getSingleton('eav/config');
Mage::app()->reinitStores();
$websites  = Mage::app()->getWebsites(false);

$scopes = array('customer', 'customer_address');
$attributes = array('firstnamekana', 'lastnamekana');
$form_code = array(
                                'checkout_register',
                                'customer_account_edit',
                                'customer_account_create',
                                'adminhtml_customer',
                                'customer_address_edit'
                                );
$form_code_address = array('adminhtml_customer_address','customer_register_address',);

foreach ($websites as $website) {
    $store = $website->getDefaultStore();
    if (!$store) {
        continue;
    }
    foreach($scopes as $scope) {
        foreach($attributes as $attribute) {
                $_attribute = $eavConfig->getAttribute($scope, $attribute);
                
                if ($scope == 'customer_address') {
                    $_attribute->setData('used_in_forms', array_merge($form_code, $form_code_address));
                } else {
                    $_attribute->setData('used_in_forms', $form_code);
                }
                $_attribute->setWebsite($website);
                $_attribute->save();
         }
    }
    
}
$installer->endSetup();