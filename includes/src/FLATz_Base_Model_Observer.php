<?php

/**
 * @license http://opensource.org/licenses/osl-3.0.php OSL-3.0
 * @author Yoshihisa AMAKATA <amakata@flatz.jp>
 * @copyright (c) 2013, FLATz Inc.
 */
class FLATz_Base_Model_Observer extends Varien_Object {

    private $_activemenu = null;
            
    /**
     * 管理画面のメニューのハイライト(ActivえMenuを設定するメソッド)
     * 
     * @param Varien_Event_Observer $observer
     * */
    public function setActiveMenu($observer) {
/*        $args = Mage::helper('flatz_base')->getObserverArgs($observer, get_class($this), __FUNCTION__);
        $path = $args->getData('path');*/
        
        if (is_null($this->_activemenu)) {
            $this->_activemenu = array();
            $activemenu = Mage::getSingleton('admin/config')->getAdminhtmlConfig()->getNode('activemenu');
            if ($activemenu) {
                foreach ($activemenu->children() as $childName => $child) {
                    $this->_activemenu[$childName] = $child;
//                    Mage::log($child);
                }
            }
        }
        $action = Mage::app()->getFrontController()->getAction();        
        if (array_key_exists(get_class($action), $this->_activemenu) && $action->getLayout()->getBlock('menu')) {
            $action->getLayout()->getBlock('menu')->setActive($this->_activemenu[get_class($action)]);
        }
        // print_r($args->getData('path'));
    }

    /**
     * setActiveMenuで使うfullActioNameを探すためのデバック処理
     * @param Varien_Event_Observer $observer
     */
    public function debugActiveMenu($observer) {

        $fullActionName = Mage::app()->getRequest()->getRequestedRouteName() . '_' .
                Mage::app()->getRequest()->getRequestedControllerName() . '_' .
                Mage::app()->getRequest()->getRequestedActionName();
        Mage::log('fullActionName: ' . $fullActionName);
    }

    /**
     * テンプレートを設定
     * @param type $observer
     */
    public function setTemplate($observer) {
         $block = $observer->getEvent()->getData('block');
         $args = Mage::helper('flatz_base')->getObserverArgs($observer, get_class($this), __FUNCTION__);
         $blockName = get_class($block);
         if(array_key_exists($blockName, (array) $args->getData())) {
             $old = $block->getTemplateFile();
             $block->setTemplate($args->getData($blockName));                
///             Mage::log('rewrite template ' . $old . ' to ' . $args->getData($blockName));
         }
    }
    
    /**
     * 
     * @param Mixed $observer
     */
   public function modifyPostData($observer) {
        $actionName = $observer->getControllerAction()->getRequest()->getActionName();
        $type = '';
        switch ($actionName) {
            case 'saveBilling':
                $type = 'billing';
                break;
            case 'saveShipping':
                $type = 'shipping';
                break;
            case 'formPost':
            case 'createpost':
                $type = '';
                break;
        }

        if (Mage::getStoreConfig(FLATz_Base_Helper_Validator::SEPARATE_POST)) {
            $this->concatPost($observer->getControllerAction()->getRequest(), $type);
        }

        if (Mage::getStoreConfig(FLATz_Base_Helper_Validator::SEPARATE_TEL)) {
            $this->concatTelFax($observer->getControllerAction()->getRequest(), $type);
        }
    }

    /**
     * 
     * @param Mage_Core_Controller_Request_Http $request
     * @param string $type
     */
    protected function concatPost($request, $type) {
        $_paramBase = 'postcode%s';
        $_request = null;
        if ($type) {
            $_request = $request->getParam($type);
        }

        $separator = Mage::getStoreConfig('flatz_base_japanese/validator/postcodeseparator');
        $postcodes = array();
        for ($i = 1; $i <= Mage::getStoreConfig('flatz_base_japanese/validator/numberofpostcodeform'); $i++) {
            $_param = sprintf($_paramBase, $i);
            if ($_request) {
                $postcodes[] = $_request[$_param];
            } else {
                $postcodes[] = $request->getParam($_param);
            }
        }

        if ($type == 'billing' || $type == 'shipping') {
            $_request['postcode'] = join($separator, $postcodes);
            $request->setPost($type, $_request);
        } else {
            $request->setPost(sprintf($_paramBase, ''), join($separator, $postcodes));
        }
    }

    /**
     * 
     * @param Mage_Core_Controller_Request_Http $request
     * @param string $type
     */
    protected function concatTelFax($request, $type) {
        $_paramBaseTel = 'telephone%s';
        $_paramBaseFax = 'fax%s';
        $_request = null;
        if ($type) {
            $_request = $request->getParam($type);
        }
        $separator = Mage::getStoreConfig('flatz_base_japanese/validator/telseparator');

        $phones = array();
        $faxes = array();
        $_nofax = 0;
        for ($i = 1; $i <= Mage::getStoreConfig('flatz_base_japanese/validator/numberoftelform'); $i++) {
            $_phone = sprintf($_paramBaseTel, $i);
            $_fax = sprintf($_paramBaseFax, $i);

            if ($_request) {
                $phones[] = $_request[$_phone];
                if (!$_request[$_fax]) {
                    $_nofax++;
                } else {
                    $faxes[] = $_request[$_fax];
                }
            } else {
                $phones[] = $request->getParam($_phone);
                if (!$request->getParam($_fax)) {
                    $_nofax++;
                } else {
                    $faxes[] = $request->getParam($_fax);
                }
            }
        }

        if ($type == 'billing' || $type == 'shipping') {
            $_request['telephone'] = join($separator, $phones);
            if ($_nofax == 0 || $_nofax == Mage::getStoreConfig('flatz_base_japanese/validator/numberoftelform')) {
                $_request['fax'] = join($separator, $faxes);
            }
            $request->setPost($type, $_request);
        } else {
            $request->setPost(sprintf($_paramBaseTel, ''), join($separator, $phones));
            if ($_nofax == 0 || $_nofax == Mage::getStoreConfig('flatz_base_japanese/validator/numberoftelform')) {
                $request->setPost(sprintf($_paramBaseFax, ''), join($separator, $faxes));
            }
        }
    }    
        
    public function restoreCheckoutMethod($observer)
    {
        $quote = $observer->getEvent()->getQuote();
        $order = $observer->getEvent()->getOrder();

        if (!$order->getCustomerId()) {
            $quote->setCheckoutMethod('guest');
        }

        return $this;
    }

    public function restoreAccountInfo($observer)
    {
        $quote = $observer->getEvent()->getQuote();
        $order = $observer->getEvent()->getOrder();
        $oldOrder = Mage::getModel('sales/order')->load($order->getRelationParentId());

        if (!$order->getCustomerId()) {
            $billing = $order->getBillingAddress();
            if(!$order->getCustomerFirstnamekana() && ($billing->getFirstnamekana() != $oldOrder->getCustomerFirstnamekana())) {
                $order->setCustomerFirstnamekana($billing->getFirstnamekana());
            } else {
                $order->setCustomerFirstnamekana($oldOrder->getCustomerFirstnamekana());
            }
            if(!$order->getCustomerLastnamekana() && ($billing->getLastnamekana() != $oldOrder->getCustomerLastnamekana())) {
                $order->setCustomerLastnamekana($billing->getLastnamekana());
            } else {
                $order->setCustomerLastnamekana($oldOrder->getCustomerLastnamekana());
            }

            $order->setCustomerFirstname($billing->getFirstname());
            $order->setCustomerLastname($billing->getLastname());

            $order->setCustomerDob($oldOrder->getCustomerDob());
            $order->setCustomerGender($oldOrder->getCustomerGender());
            $order->setCustomerGroupId(0);
            $order->save();
        }


        return $this;
    }
    /**
     * 注文コメントのヘルパメソッド
     * @param type $observer
     */
    public function setCustomerOrderComment($observer)
    {
        $orderComment = Mage::app()->getRequest()->getPost('customerOrderComment');

        $orderComment = trim($orderComment);
	$order = $observer->getEvent()->getOrder();
	if ($order) {
	  $order->setCustomerordercomment($orderComment);
        }
    }    
}
