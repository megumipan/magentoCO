<?php

class Rack_Point_Block_Adminhtml_Import_Form extends Mage_Adminhtml_Block_Widget_Form_Container
{
    protected function _construct()
    {
        $this->_blockGroup = 'rackpoint';
        $this->_controller = 'adminhtml_import';
        $this->_mode       = 'upload';
        
        $this->_headerText = $this->__('Import point - Upload data file');
    }
    
    protected function _prepareLayout()
    {
        $this->_removeButton('save')
             ->_removeButton('back')
             ->_removeButton('reset');
        
        return parent::_prepareLayout();
    }
}