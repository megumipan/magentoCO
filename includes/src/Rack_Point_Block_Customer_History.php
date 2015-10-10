<?php

class Rack_Point_Block_Customer_History extends Mage_Core_Block_Template
{

    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('rackpoint/customer/history.phtml');

        $histories = Mage::getResourceModel('rackpoint/point_history_collection')
            ->addFieldToSelect('*')
            ->addFieldToFilter('customer_id', Mage::getSingleton('customer/session')->getCustomer()->getId())
            ->addFieldToFilter('website_id', Mage::app()->getStore()->getWebsiteId())
            ->setOrder('id', 'desc')
        ;

        $this->setHistories($histories);
    }

    protected function _prepareLayout()
    {
        parent::_prepareLayout();

        $pager = $this->getLayout()->createBlock('page/html_pager', 'rackpoint.history.pager')
                      ->setCollection($this->getHistories());
        $this->setChild('pager', $pager);
        $this->getHistories()->load();
        return $this;
    }

    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }

    public function getBackUrl()
    {
        return $this->getUrl('customer/account/');
    }
    
}