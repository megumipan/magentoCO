<?php

class Maruweb_Digitalcheck_Model_Cron
{
    private $_checkStatus   = 'pending_payment';
    private $_checkPaymentMethod = array('digitalcheck_cvs', 'digitalcheck_pe');

    public function removeOutofdateorder($schedule) {
        $_orders = $this->_getOutofdateorders();

        Mage::register('isSecureArea', true, true);

        foreach ($_orders as $_key => $_order) {
			$payment = $_order->getPayment();
			if (in_array($payment->getMethod(), $this->_checkPaymentMethod)) {
				Mage::Log("delete :".$_order->getEntityId());
				$_order->setState(
				Mage_Sales_Model_Order::STATE_CANCELED,
				true,
				Mage::helper('digitalcheck')->__('Expired.'),
				true
				);
				$_order->save();
			}
        }
    }


    protected function _getOutofdateorders() {
        $expire_date = date('Ymd');
        //$expire_time->sub($_outOfDateTerm, Zend_Date::MINUTE);
        $_orders = Mage::getResourceModel('sales/order_collection')->addAttributeToSelect('*')
                   ->addAttributeToFilter('status', $this->_checkStatus)
                   ->addAttributeToFilter('pay_limit_date', array("lt" => $expire_date));

        return $_orders;
    }

    protected function _processResult($result)
    {
        if ($result['result'] == '1') {
            return;
        }
        $_order = Mage::getModel('sales/order')->loadByIncrementId($result['trading_id']);

        switch ($result['payment_status']) {
            case '12':
            case '15':
            case '16':
            case '61':
                $this->_cancelOrder($_order);
                break;

            case '40':
                $this->_addPaymentId($_order, $result);
                $this->_createInvoice($_order);
                break;
            case '10':
            case '43':
                $this->_createInvoice($_order);
                break;

        }

    }

    protected function _addPaymentId($order, $result)
    {
        $payment = $order->getPayment();
        $payment->setCcTransId($result["payment_id"]);
        $order->save();
    }

    protected function _createInvoice($order)
    {
        $items = array();

        foreach ($order->getAllItems() as $item) {
            $items[$item->getId()] = $item->getQtyOrdered();
        }

        $invoice = Mage::getModel('sales/service_order', $order)->prepareInvoice($items);
        $invoice->sendEmail(true, '')->pay();
        $invoice->register()->setEmailSent(true);
        $invoice->getOrder()->setCustomerNoteNotify(true)->setIsInProcess(true);

        $transactionSave = Mage::getModel('core/resource_transaction')
            ->addObject($invoice)
            ->addObject($order);
        $transactionSave->save();
    }

    protected function _cancelOrder($order)
    {
        $order->cancel();
    }
}
