<?php

class Rack_Point_Block_Adminhtml_Rule extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_addButton('apply_rules', array(
            'label'     => Mage::helper('rackpoint')->__('Apply Rules'),
            'onclick'   => "location.href='".$this->getUrl('*/*/applyRules')."'",
            'class'     => '',
        ));

        $this->_controller = 'adminhtml_rule';
        $this->_blockGroup = 'rackpoint';
        $this->_headerText = Mage::helper('rackpoint')->__('Manage Point Rule');
        $this->_addButtonLabel = Mage::helper('rackpoint')->__('Add New Rule');
        parent::__construct();

    }
}