<?php
class Maruweb_Digitalcheck_Block_Info_Pe extends Mage_Payment_Block_Info {

    protected function _prepareSpecificInformation($transport = null)
    {
    	if (null !== $this->_paymentSpecificInformation) {
            return $this->_paymentSpecificInformation;
        }
        $transport = parent::_prepareSpecificInformation($transport);
        $data = array();
        if ($receiptNo = $this->getInfo()->getPeReceiptNumber()) {
            $data[Mage::helper('digitalcheck')->__('Receipt No Pe')] = $this->escapeHtml($receiptNo);
        }

        if ($receiptLimit = $this->getInfo()->getPayLimitDate()) {
            $data[Mage::helper('digitalcheck')->__('Pay Limit Date')] = preg_replace('/^(\d\d\d\d)(\d\d)(\d\d)$/', '$1/$2/$3', $receiptLimit);
        }

        if ($receiptUrl = $this->getInfo()->getPeReceiptUrl()) {
            //$data[Mage::helper('digitalcheck')->__('Receipt Url')] = Mage::helper('digitalcheck')->__('<a href="%s" target="_blank"> Go to Receipt Page</a>', $receiptUrl);
            //$data[Mage::helper('digitalcheck')->__('Receipt Url')] = sprintf('<a href="%s" target="_blank"> Go to Receipt Page</a>', $receiptUrl);
            $data[Mage::helper('digitalcheck')->__('Receipt Url')] = Mage::helper('digitalcheck')->__($receiptUrl);
        }
        return $transport->setData(array_merge($data, $transport->getData()));
    }
}
