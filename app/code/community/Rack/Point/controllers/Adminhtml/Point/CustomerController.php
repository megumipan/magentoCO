<?php

class Rack_Point_Adminhtml_Point_CustomerController extends Mage_Adminhtml_Controller_Action
{
    public function indexAction()
    {
        $this->loadLayout();
        $this->_title($this->__('Manage Balance'))->_title($this->__('Rack Point'));
        $this->_setActiveMenu('rackpoint/point_balance');
        
        $block = $this->getLayout()->createBlock('rackpoint/adminhtml_customer_balance', 'rackpoint.customer.balance');
        
        $this->_addContent($block);
        $this->renderLayout();
    }
    
    public function historyGridAction()
    {
        $this->loadLayout();
        $block = $this->getLayout()->createBlock('rackpoint/adminhtml_customer_tab_history_grid', 'rackpoint.tab.history.grid');
        $block->setCustomerId($this->getRequest()->getParam('customer_id'));
        $this->getResponse()->setBody($block->getHtml());
    }
    
    public function balanceGridAction()
    {
        $this->loadLayout();
        $block = $this->getLayout()->createBlock('rackpoint/adminhtml_customer_balance_grid', 'rackpoint.balance.grid');
        $this->getResponse()->setBody($block->getHtml());
    }
    
    /**
     * Export customer grid to CSV format
     */
    public function exportCsvAction()
    {
        $fileName   = 'points.csv';
        $content    = $this->getLayout()->createBlock('rackpoint/adminhtml_customer_balance_grid')->getCsvFile();

        $this->_prepareDownloadResponse($fileName, $content);
    }
    
    public function renewAction()
    {
        if ($this->getRequest()->isPost()) {
            $customerId = $this->getRequest()->getPost('customer_id');
            $websiteId  = $this->getRequest()->getPost('website_id');
            try {
                $balance = Mage::getModel('rackpoint/point_balance');
                $balance->renew($customerId, $websiteId);
                
                $this->_getSession()->addSuccess($this->__('Renew point balance successfull.'));
            } catch (Exception $e) {
                $this->_getSession()->addError($this->__($e->getMessage()));
            }
        }
        
        $this->_redirect('*/customer/edit', array('id' => $customerId, 'tab' => 'customer_info_tabs_rackpoint_customer_point'));
    }

    public function massDeleteAction()
    {
        if ($this->getRequest()->isPost()) {
            $balanceIds = $this->getRequest()->getPost('balance_id');
            try {
                $balance = Mage::getModel('rackpoint/point_balance');
                foreach ($balanceIds as $balanceId) {
                    $balance->setId($balanceId);
                    $balance->delete();
                }

                $this->_getSession()->addSuccess($this->__('Delete point balance successfull.'));
            } catch (Exception $e) {
                $this->_getSession()->addError($this->__($e->getMessage()));
            }
        }
        $this->_redirect('*/*');
    }

    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('rackpoint/pointrule');
    }
}