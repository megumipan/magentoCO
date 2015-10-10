<?php
/**
 * @license http://opensource.org/licenses/osl-3.0.php OSL-3.0
 * @author Yoshihisa AMAKATA <amakata@flatz.jp>
 * @copyright (c) 2013, FLATz Inc.
 */
class FLATz_Base_Model_Install extends Mage_Core_Model_Abstract
{
    
    public function install($mode, $website, $store)
    {
        $value = (string) Mage::getConfig()->getNode('default/flatz_base_japanese/address_templates/' . $mode);
        
        Mage::getModel('adminhtml/config_data')
            ->setSection('customer/address_templates/' . $mode)
            ->setWebsite($website)
            ->setStore($store)
            ->setGroups($value)
            ->save();
    }
    
    public function restore($mode, $website, $store)
    {
        $value = (string) Mage::getConfig()->getNode('default/customer/address_templates/' . $mode);
        Mage::getModel('adminhtml/config_data')
            ->setSection('customer/address_templates/' . $mode)
            ->setWebsite($website)
            ->setStore($mode)
            ->setGroups($value)
            ->save();
    }
}