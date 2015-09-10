<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * @category  Techbandhu
 * @package   Techbandhu_Asktologin
 * @author 	  TechBandhu <techbandhus@gmail.com>
 */
class Techbandhu_Asktologin_AjaxController extends Mage_Core_Controller_Front_Action
{
    public function preDispatch()
    {
        parent::preDispatch();
    }
    
    /**
     * @uses ajax login
     * @author TechBandhu <techbandhus@gmail.com>
     * */
    public function loginAction(){
    	$username = $this->getRequest()->getPost('asktologin_username', false);
        $password = $this->getRequest()->getPost('asktologin_password',  false);
        $session = Mage::getSingleton('customer/session');

        $result = array('success' => false);

        if ($username && $password) {
            try {
                $session->login($username, $password);
            } catch (Exception $e) {
                $result['error'] = $e->getMessage();
            }
            if (! isset($result['error'])) {
                $result['success'] = true;
            }
        } else {
            $result['error'] = $this->__(
            'Please enter a username and password.');
        }
        $this->getResponse()->setBody(Zend_Json::encode($result));
    }

}