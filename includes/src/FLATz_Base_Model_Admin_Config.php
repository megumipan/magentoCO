<?php

/**
 * @license http://opensource.org/licenses/osl-3.0.php OSL-3.0
 * @author Yoshihisa AMAKATA <amakata@flatz.jp>
 * @copyright (c) 2013, FLATz Inc.
 */
class FLATz_Base_Model_Admin_Config extends Mage_Admin_Model_Config {
    
    public function __construct()
    {
        // 親クラスの初期化をしないように修正した
        Varien_Simplexml_Config::__construct();
        $this->setCacheId('adminhtml_acl_menu_config');

        /* @var $adminhtmlConfig Varien_Simplexml_Config */
        $adminhtmlConfig = Mage::app()->loadCache($this->getCacheId());
        if ($adminhtmlConfig) {
            $this->_adminhtmlConfig = new Varien_Simplexml_Config($adminhtmlConfig);
        } else {
            $adminhtmlConfig = new Varien_Simplexml_Config;
            $adminhtmlConfig->loadString('<?xml version="1.0"?><config></config>');
            Mage::getConfig()->loadModulesConfiguration('adminhtml.xml', $adminhtmlConfig);
            // adminhtmlの拡張ファイル読み込み
            if (Mage::getStoreConfig('flatz_base_admin/menu/use_extend')) {
                Mage::getConfig()->loadModulesConfiguration('adminhtml-ext.xml', $adminhtmlConfig);
            }
            $this->_adminhtmlConfig = $adminhtmlConfig;

            /**
             * @deprecated after 1.4.0.0-alpha2
             * support backwards compatibility with config.xml
             */
            $aclConfig  = Mage::getConfig()->getNode('adminhtml/acl');
            if ($aclConfig) {
                $adminhtmlConfig->getNode()->extendChild($aclConfig, true);
            }
            $menuConfig = Mage::getConfig()->getNode('adminhtml/menu');
            if ($menuConfig) {
                $adminhtmlConfig->getNode()->extendChild($menuConfig, true);
            }

            if (Mage::app()->useCache('config')) {
                Mage::app()->saveCache($adminhtmlConfig->getXmlString(), $this->getCacheId(),
                    array(Mage_Core_Model_Config::CACHE_TAG));
            }
        }
    }

}
