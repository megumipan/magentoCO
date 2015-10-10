<?php

class Rack_Point_Block_Adminhtml_Import_Preview_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    protected function _construct()
    {
        $this->setUseAjax(true);
        $this->setId('preview_grid');
        $this->setSaveParametersInSession(true);
        
        parent::_construct();
    }
    protected function _prepareCollection()
    {
        $filename = Mage::getSingleton('adminhtml/session')->getPointDataFile();
        $encoding = Mage::getStoreConfig('rackpoint/import/encoding');
        $collection = new Rack_Point_Model_Csv_Collection($filename, ',', $encoding);
        
        $this->setCollection($collection);
        
        return parent::_prepareCollection();
        
    }
    protected function _prepareColumns()
    {
        $this->addColumn('customer_email', array(
            'header'    => $this->__('Email'),
            'index'     => 'email',
            'sortable'  => false,
            'filter'    => false,
            'width'     => 250,
        ));
        
        $this->addColumn('point', array(
            'header'    => $this->__('Point'),
            'index'     => 'point',
            'sortable'  => false,
            'filter'    => false,
            'type'      => 'number',
            'width'     => 150,
        ));

        $this->addColumn('updated_at', array(
            'header'    => $this->__('Last Update Date'),
            'index'     => 'updated_at',
            'sortable'  => false,
            'filter'    => false,
            'width'     => 150,
        ));

        $this->addColumn('expired_at', array(
            'header'    => $this->__('Expired Date'),
            'index'     => 'expired_at',
            'sortable'  => false,
            'filter'    => false,
            'width'     => 150,
        ));

        $this->addColumn('comment', array(
            'header'    => $this->__('Comment'),
            'index'     => 'comment',
            'sortable'  => false,
            'filter'    => false,
        ));
        
        parent::_prepareColumns();
    }
    
    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', array('_current' => true));
    }
}