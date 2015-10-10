<?php

class Rack_Point_Block_Adminhtml_Import_Preview extends Mage_Adminhtml_Block_Widget_Container
{
    public function __construct()
    {
        parent::__construct();
        
        $this->_blockGroup = 'rackpoint';
        $this->_controller = 'adminhtml_import_preview';
        $this->_headerText = $this->__('Import point - Data Preview');
        
        $this->_removeButton('add');
        
        $this->addButton('back', array(
            'class'     => 'button back',
            'label'     => $this->__('Back'),
            'onclick'   => "setLocation('" . $this->getUrl('*/*') . "');"
        ));
        $this->_addButton('import', array(
            'class'     => 'button save',
            'label'     => $this->__('Import'),
            'onclick'   => 'doImport();',
        ));
    }
    
    public function getGridHtml()
    {
        $grid = $this->getLayout()->createBlock('rackpoint/adminhtml_import_preview_grid', 'preview.grid');
        
        return $grid->toHtml();
    }
    
    public function getWebsiteCollection()
    {
        return Mage::app()->getWebsites();
    }

    public function getGroupCollection(Mage_Core_Model_Website $website)
    {
        return $website->getGroups();
    }

    public function getStoreCollection(Mage_Core_Model_Store_Group $group)
    {
        return $group->getStores();
    }
}