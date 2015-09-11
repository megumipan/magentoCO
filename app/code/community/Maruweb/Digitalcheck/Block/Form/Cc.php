<?php
class Maruweb_Digitalcheck_Block_Form_Cc extends Mage_Payment_Block_Form {
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('digitalcheck/form/cc.phtml');
    }

    public function getStoreNumber()
    {
        $configData = array(
        	"10_1" => Mage::helper("digitalcheck")->__("One Time"),
        	"61_3" => Mage::helper("digitalcheck")->__("3Times"),
        	"61_5" => Mage::helper("digitalcheck")->__("5Times"),
        	"61_6" => Mage::helper("digitalcheck")->__("6Times"),
        	"61_10" => Mage::helper("digitalcheck")->__("10Times"),
        	"61_12" => Mage::helper("digitalcheck")->__("12Times"),
        	"61_15" => Mage::helper("digitalcheck")->__("15Times"),
        	"61_18" => Mage::helper("digitalcheck")->__("18Times"),
        	"61_20" => Mage::helper("digitalcheck")->__("20Times"),
        	"61_24" => Mage::helper("digitalcheck")->__("24Times"),
        	"80_1" => Mage::helper("digitalcheck")->__("Revolving")
        );
        return $configData;
    }
}
