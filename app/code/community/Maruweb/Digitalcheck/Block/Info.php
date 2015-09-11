<?php
class Maruweb_Digitalcheck_Block_Info extends Mage_Core_Block_Template
{

    public function getReceiptUrl()
    {
        return Mage::getSingleton('checkout/session')->getReceiptUrl();
    }
    public function getLimitDate()
    {
        return Mage::getSingleton('checkout/session')->getLimitDate();
    }
    public function getMethodCode()
    {
    	return Mage::getSingleton('checkout/session')->getMethodCode();
    }
    /*
    protected function _beforeToHtml()
    {
        $this->_preparePaymentInfo();
        return parent::_beforeToHtml();
    }

    protected function _preparePaymentInfo()
    {
        $orderId = Mage::getSingleton('checkout/session')->getLastOrderId();
        if ($orderId) {
            $order = Mage::getModel('sales/order')->load($orderId);
            if ($order->getId()) {
                $payment = $order->getPayment()->getMethodInstance();

                $this->addData(array(
                    'receipt_url'  => $payment->getAspUrl(),
                ));
            }
        }
    }
    */
}