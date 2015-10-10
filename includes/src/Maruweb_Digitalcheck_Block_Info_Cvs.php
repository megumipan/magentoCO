<?php
class Maruweb_Digitalcheck_Block_Info_Cvs extends Mage_Payment_Block_Info {

    protected function _prepareSpecificInformation($transport = null)
    {
    	if (null !== $this->_paymentSpecificInformation) {
            return $this->_paymentSpecificInformation;
        }
        $transport = parent::_prepareSpecificInformation($transport);
        $data = array();
        if ($dcCvs = $this->getInfo()->getDcCvs()) {
            $source = Mage::getModel('digitalcheck/source_cvstype');
            $data[Mage::helper('digitalcheck')->__('CVS Method')] = $this->getCvsType($dcCvs);
        }
        if ($receiptNo = $this->getInfo()->getCvsReceiptNumber()) {
            $source = Mage::getModel('digitalcheck/source_cvstype');
            $data[Mage::helper('digitalcheck')->__($this->getCvsMethodDescription($dcCvs))] = $this->escapeHtml($receiptNo);
        }

        if ($receiptLimit = $this->getInfo()->getPayLimitDate()) {
            $data[Mage::helper('digitalcheck')->__('Pay Limit Date')] = preg_replace('/^(\d\d\d\d)(\d\d)(\d\d)$/', '$1/$2/$3', $receiptLimit);
        }

        if ($receiptUrl = $this->getInfo()->getCvsReceiptUrl()) {
            $source = Mage::getModel('digitalcheck/source_cvstype');
            //$data[Mage::helper('digitalcheck')->__('Receipt Url')] = sprintf('<a href="%s" target="_blank"> Go to Receipt Page</a>', Mage::helper('digitalcheck')->__($receiptUrl));
            $data[Mage::helper('digitalcheck')->__('Receipt Url')] = Mage::helper('digitalcheck')->__($receiptUrl);
        }
        return $transport->setData(array_merge($data, $transport->getData()));
    }

    public function getCvsType($cvsType) {
        $code = "";
        switch ($cvsType) {
            case '1' :
                $code = Mage::helper('digitalcheck')->__('LoppiPayment');
                break;
            case '2' :
                $code = Mage::helper('digitalcheck')->__('SevenElevenPayment');
                break;
            case '3' :
                $code = Mage::helper('digitalcheck')->__('FamilyMartPayment');
                break;
            case '71' :
                $code = Mage::helper('digitalcheck')->__('ACDPayment');
                break;
            case '73' :
                $code = Mage::helper('digitalcheck')->__('CVSOnlinePayment');
                break;
        }

        return $code;
    }

    public function getCvsMethodDescription($cvsType) {
        $code = "Receipt No";
        switch ($cvsType) {
            case '1' :
            case '2' :
            case '3' :
            case '73' :
                $code = Mage::helper('digitalcheck')->__(sprintf('Receipt No %s', $cvsType));
                break;
        }

        return $code;
    }
}
