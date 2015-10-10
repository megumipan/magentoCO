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
class Techbandhu_Asktologin_Block_Asktologin extends Mage_Core_Block_Template
{
	public function _toHtml()
	{
		$html = parent::_toHtml();
		if(!$this->canAsktologin()){
			$html = "";
		}
		return $html;
	}
	
	public function canAccess()
	{
		// Set Open Action/controller/Modules
		$action = $this->getRequest()->getActionName();
		$controller = $this->getRequest()->getControllerName();
		$module = $this->getRequest()->getModuleName();
		
		$openModule = array('customer');
		$openController = array('account');
	    $openActions = array('forgotpassword','create');
	    
	    $modulePattern = '/^(' . implode('|', $openModule) . ')/i';
	    $controllerPattern = '/^(' . implode('|', $openController) . ')/i';
	    $actionPattern = '/^(' . implode('|', $openActions) . ')/i';
	    
	    $shouldAsk = false;
	    
	    if(!Mage::getSingleton("customer/session")->isLoggedIn())
	    {
    		if (!preg_match($modulePattern, $module) && !preg_match($controllerPattern, $controller) && !preg_match($actionPattern, $action)) 
		    {
		    	$shouldAsk = true;
		    }
		    
		    if(Mage::getStoreConfig('tb_asktologin/asktologin_option/if_new')){
		    	if(Mage::getModel('core/cookie')->get("asktologin")==1)
		    		$shouldAsk = false;
		    }
    	}
	    return $shouldAsk;
	}

	public function canAsktologin()
	{
		$shouldAsk = false;
		$config = Mage::getStoreConfig('tb_asktologin/asktologin_option/is_active');
		if($config){
			$shouldAsk = $this->canAccess();
		}
		return $shouldAsk;
	}
	
	public function newCustomer($confVariable){
		$data = "false";
		$config = Mage::getStoreConfig('tb_asktologin/asktologin_option/if_new');
		if($config){
			$period = 3600 * 24; // for next 24 hour
			Mage::getModel("core/cookie")->set("asktologin", "1", 7200);
			$data = "true";
		}
		return $data;
	}
	
	public function getLoginPostActionUrl()
    {
        return Mage::getUrl('asktologin/ajax/login', array('_secure'=>true));
    }
    
    public function getForgotPasswordUrl()
    {
        return $this->helper('customer')->getForgotPasswordUrl();
    }
    
    public function getCreateAccountUrl()
    {
        return $this->helper('customer')->getRegisterUrl();
    }
}