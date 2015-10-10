<?php

class Rack_Point_Block_Adminhtml_Customer_History_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * Internal constructor
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setUseAjax(true);
        $this->setId('rackpointHistoryGrid');
    }

    /**
     * Prepare grid collection object
     *
     * @return Rack_Point_Block_Adminhtml_Customer_History_Grid
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getModel('rackpoint/point_history')->getCollection()
            ->addCustomerInfo()
            ->setOrder('id', 'desc');
        
        $this->setCollection($collection);
        
        return parent::_prepareCollection();
    }

    /**
     * Prepare grid columns
     *
     * @return Mage_Widget_Block_Adminhtml_Widget_Instance_Grid
     */
    protected function _prepareColumns()
    {
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
        
        $this->addColumn('ref_id', array(
            'index'    => 'ref_id',
            'header'   => Mage::helper('rackpoint')->__('Ref #'),
            'width'    => '80px',
        ));

        $this->addColumn('action', array(
            'header'   => Mage::helper('rackpoint')->__('Action'),
            'width'    => 1,
            'index'    => 'action',
            'type'     => 'options',
            //'renderer' => 'rackpoint/adminhtml_customer_history_grid_column_renderer_action'
            'options'  => array(
                 Rack_Point_Model_Point_History::ACTION_BY_INVOICE                => $this->__('invoiced'),
                 Rack_Point_Model_Point_History::ACTION_BY_RECEIVED_REFUNDED      => $this->__('reveived_refunded'),
                 Rack_Point_Model_Point_History::ACTION_BY_USED_REFUNDED          => $this->__('used_refunded'),
                 Rack_Point_Model_Point_History::ACTION_BY_PLACE_ORDER            => $this->__('place_order'),
                 Rack_Point_Model_Point_History::ACTION_BY_REGISTER               => $this->__('register'),
                 Rack_Point_Model_Point_History::ACTION_BY_WRITE_REVIEW           => $this->__('write_review'),
                 Rack_Point_Model_Point_History::ACTION_BY_NEWSLETTER             => $this->__('newsletter_subscribed'),
                 Rack_Point_Model_Point_History::ACTION_BY_CANCEL_ORDER           => $this->__('cancel_order'),
                 Rack_Point_Model_Point_History::ACTION_BY_ADMIN                  => $this->__('moderation'),
                 Rack_Point_Model_Point_History::ACTION_IMPORT                    => $this->__('import')
            )
        ));
        
        $this->addColumn('point', array(
            'type'     => 'number',
            'rate'     => 1,
            'index'    => 'point',
            'header'   => Mage::helper('rackpoint')->__('Point Delta'),
            'width'    => 1,
            'renderer' => 'rackpoint/adminhtml_customer_history_grid_column_renderer_formattedNumber',
            //'show_number_sign' => 1
        ));

        $this->addColumn('currency', array(
            'header'   => Mage::helper('rackpoint')->__('Currency'),
            'sortable' => false,
            'filter'   => false,
            'width'    => 1,
            'align'    => 'right',
            'renderer' => 'rackpoint/adminhtml_customer_history_grid_column_renderer_currency'
        ));

        $this->addColumn('rate', array(
            'getter'   => 'getRateDescription',
            'header'   => Mage::helper('rackpoint')->__('Point Rate'),
            'sortable' => false,
            'filter'   => false,
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

        $this->addColumn('comment', array(
            'index'    => 'comment',
            'header'   => Mage::helper('rackpoint')->__('Comment'),
            'align'    => 'left'
        ));
        
        $this->addColumn('created_at', array(
            'type'     => 'datetime',
            'index'    => 'created_at',
            'header'   => Mage::helper('rackpoint')->__('Created At'),
            'align'    => 'left',
            'html_decorators' => 'nobr',
        ));

        return parent::_prepareColumns();
    }
    
//    protected function _prepareMassaction()
//    {
//        $this->setMassactionIdField('id');
//        $this->getMassactionBlock()->setFormFieldName('history_id');
//
//        $this->getMassactionBlock()->addItem('delete', array(
//             'label'    => Mage::helper('rackpoint')->__('Delete'),
//             'url'      => $this->getUrl('*/*/massDelete'),
//             'confirm'  => Mage::helper('rackpoint')->__('Are you sure?')
//        ));
//
//        return $this;
//    }

    /**
     * Return grid url for ajax actions
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('adminhtml/point_history/historyGrid');
    }
    
    public function getRowUrl($row)
    {
        return $this->getUrl('*/customer/edit', array('id' => $row->getCustomerId(), 'tab' => 'customer_info_tabs_rackpoint_customer_point'));
    }
}
