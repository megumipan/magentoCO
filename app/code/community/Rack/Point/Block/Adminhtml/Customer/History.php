<?php

class Rack_Point_Block_Adminhtml_Customer_History extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct() 
    {
        $this->_controller = 'adminhtml_customer_history';
        $this->_blockGroup = 'rackpoint';
        $this->_headerText = Mage::helper('rackpoint')->__('Point Transaction History');
        
        parent::__construct();
        $this->_removeButton('add');
    }
}
