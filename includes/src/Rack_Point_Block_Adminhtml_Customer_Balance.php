<?php

class Rack_Point_Block_Adminhtml_Customer_Balance extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct() 
    {
        $this->_controller = 'adminhtml_customer_balance';
        $this->_blockGroup = 'rackpoint';
        $this->_headerText = Mage::helper('rackpoint')->__('Point Balance');
        
        parent::__construct();
        $this->_removeButton('add');
    }
}
