<?php
class Maruweb_Digitalcheck_Block_Form_Cvs extends Mage_Payment_Block_Form {
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('digitalcheck/form/cvs.phtml');
    }

    public function getStoreNumber()
    {
        $config = preg_split("/,/", Mage::getStoreConfig('payment/digitalcheck_cvs/cvstype'));
        $keys = Mage::getModel('digitalcheck/source_cvstype')->toOptionArray();
        $configData = array();

        foreach ($keys as $entry) {
            if (in_array($entry["value"], $config)) {
                $configData[$entry["value"]] = $entry["label"];
            }
        }
        return $configData;
    }
}
