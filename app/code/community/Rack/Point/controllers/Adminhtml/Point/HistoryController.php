<?php

class Rack_Point_Adminhtml_Point_HistoryController extends Mage_Adminhtml_Controller_Action
{
    public function indexAction()
    {
        $this->loadLayout();
        $this->_title($this->__('Manage Point History'))->_title($this->__('Rack Point'));
        $this->_setActiveMenu('rackpoint/point_history');
        $block = $this->getLayout()->createBlock('rackpoint/adminhtml_customer_history', 'rackpoint.customer.history');
        
        $this->_addContent($block);
        $this->renderLayout();
    }
    
    public function historyGridAction()
    {
        $this->loadLayout();
        $block = $this->getLayout()->createBlock('rackpoint/adminhtml_customer_history_grid', 'rackpoint.history.grid');
        $this->getResponse()->setBody($block->getHtml());
    }
    
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('rackpoint/pointrule');
    }
}