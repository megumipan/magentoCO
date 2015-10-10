<?php

class Rack_Point_Block_Adminhtml_Customer_Tab_History extends Rack_Point_Block_Adminhtml_Customer_Account
{
    /**
     * Internal constructor
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('rackpoint/customer/history.phtml');
    }

    /**
     * Prepare layout.
     * Create history grid block
     *
     * @return Rack_Point_Block_Adminhtml_Customer_History
     */
    protected function _prepareLayout()
    {
        $grid = $this->getLayout()
            ->createBlock('rackpoint/adminhtml_customer_tab_history_grid')
            ->setCustomerId($this->getCustomer()->getId());
        $this->setChild('grid', $grid);
        
        return parent::_prepareLayout();
    }
}
