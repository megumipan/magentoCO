<?php

class Rack_Point_Block_Adminhtml_Customer_Balance_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('customerPointBalanceGrid');
        $this->setDefaultSort('id');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
    }
    
    protected function _prepareCollection()
    {
        $collection = Mage::getModel('rackpoint/point_balance')->getCollection();
        $collection->addCustomerInfo();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }
    
     protected function _prepareColumns()
    {
        $this->addColumn('id', array(
            'header'    => Mage::helper('rackpoint')->__('ID'),
            'align'     =>'right',
            'width'     => '30px',
            'index'     => 'id',
            'type'      => 'number',
            'is_system' => true,
         ));
        
        if (Mage::app()->getLocale()->getLocaleCode() == 'ja_JP') {
            $this->addColumn('customer_lastname', array(
                'header'    => Mage::helper('rackpoint')->__('Lastname'),
                'align'     => 'left',
                'width'     => '150px',
                'index'     => 'customer_lastname',
                'header_export' => 'customer_lastname',
                'filter_index' => 'clt.value',
            ));
            
            $this->addColumn('customer_firstname', array(
                'header'    => Mage::helper('rackpoint')->__('First Name'),
                'align'     =>'left',
                'width'     => '150px',
                'index'     => 'customer_firstname',
                'header_export' => 'customer_firstname',
                'filter_index' => 'cft.value',
            ));

        } else {
            $this->addColumn('customer_firstname', array(
                'header'    => Mage::helper('rackpoint')->__('First Name'),
                'align'     =>'left',
                'width'     => '150px',
                'index'     => 'customer_firstname',
                'header_export' => 'customer_firstname',
                'filter_index' => 'cft.value',
            ));

            $this->addColumn('customer_lastname', array(
                'header'    => Mage::helper('rackpoint')->__('Lastname'),
                'align'     => 'left',
                'width'     => '150px',
                'index'     => 'customer_lastname',
                'header_export' => 'customer_lastname',
                'filter_index' => 'clt.value',
            ));
        }
        $this->addColumn('customer_email', array(
            'header'    => Mage::helper('rackpoint')->__('Email'),
            'align'     => 'left',
            'width'     => '200px',
            'index'     => 'customer_email',
            'header_export' => 'email',
            'filter_index' => 'email',
        ));
      
        $this->addColumn('balance', array(
            'header'    => Mage::helper('rackpoint')->__('Balance'),
            'align'     => 'left',
            'width'     => '100px',
            'index'     => 'balance',
            'type'      => 'number',
            'header_export' => 'balance'
        ));
        
        if (!Mage::app()->isSingleStoreMode()) {
            $this->addColumn('website_id', array(
                'header'    => Mage::helper('rackpoint')->__('Website'),
                'align'     => 'center',
                'width'     => '80px',
                'type'      => 'options',
                'options'   => Mage::getResourceSingleton('core/website_collection')->toOptionHash(),
                'index'     => 'website_id',
                'filter_index' => 'main_table.website_id',
                'header_export' => 'website_id'
            ));
        }
        
        $this->addColumn('updated_at', array(
            'header'    => Mage::helper('rackpoint')->__('Updated At'),
            'align'     => 'left',
            'width'     => '80px',
            'index'     => 'updated_at',
            'html_decorators' => 'nobr',
            'type'      => 'datetime',
            'header_export' => 'updated_at',
            'getter'   => 'getUpdatedAtEx'
        ));
        
        $this->addColumn('expired_at', array(
            'header'    => Mage::helper('rackpoint')->__('Expired At'),
            'align'     => 'left',
            'width'     => '80px',
            'index'     => 'expired_at',
            'html_decorators' => 'nobr',
            'type'      => 'datetime',
            'header_export' => 'expired_at'
        ));
        
        $this->addColumn('expired', array(
            'header'    => Mage::helper('rackpoint')->__('Expired'),
            'align'     => 'left',
            'width'     => '50px',
            'sortable'  => false,
            'filter'   => false,
            'renderer' => 'rackpoint/adminhtml_customer_balance_grid_column_renderer_expired',
            'is_system' => true,
        ));

        $this->addExportType('*/*/exportCsv', Mage::helper('rackpoint')->__('CSV'));
        
      return parent::_prepareColumns();
    }
    
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('id');
        $this->getMassactionBlock()->setFormFieldName('balance_id');

        $this->getMassactionBlock()->addItem('delete', array(
             'label'    => Mage::helper('rackpoint')->__('Delete'),
             'url'      => $this->getUrl('*/*/massDelete'),
             'confirm'  => Mage::helper('rackpoint')->__('Are you sure?')
        ));

        return $this;
    }
    
    public function getGridUrl()
    {
        return $this->getUrl('*/*/balanceGrid', array('_current'=>true));
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('*/customer/edit', array('id' => $row->getCustomerId(), 'tab' => 'customer_info_tabs_rackpoint_customer_point'));
    }
}