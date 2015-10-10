<?php

class Rack_Point_Block_Adminhtml_Customer_Tab_History_Grid extends Mage_Adminhtml_Block_Widget_Grid
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
            ->addFieldToFilter('customer_id', $this->getCustomerId())
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
        $this->addColumn('ref_id', array(
            'index'    => 'ref_id',
            'header'   => Mage::helper('rackpoint')->__('Ref #'),
            'sortable' => false,
            'width'    => '80px',
        ));
        
        if (!Mage::app()->isSingleStoreMode()) {
            $this->addColumn('website_id', array(
                'header'    => Mage::helper('rackpoint')->__('Website'),
                'width'     => '150px',
                'type'      => 'options',
                'options'   => Mage::getResourceSingleton('core/website_collection')->toOptionHash(),
                'index'     => 'website_id',
                'sortable' => false,
                'filter'   => false,
            ));
        }

        $this->addColumn('action', array(
            'header'   => Mage::helper('rackpoint')->__('Action'),
            'sortable' => false,
            'filter'   => false,
            'width'    => 1,
            'index'    => 'action',
            'renderer' => 'rackpoint/adminhtml_customer_history_grid_column_renderer_action'
        ));
        
        $this->addColumn('point', array(
            'type'     => 'number',
            'rate'     => 1,
            'index'    => 'point',
            'header'   => Mage::helper('rackpoint')->__('Point Delta'),
            'sortable' => false,
            'filter'   => false,
            'width'    => 1,
            'renderer' => 'rackpoint/adminhtml_customer_history_grid_column_renderer_formattedNumber',
            //'show_number_sign' => true
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
            'filter'   => false
        ));

        $this->addColumn('comment', array(
            'index'    => 'comment',
            'header'   => Mage::helper('rackpoint')->__('Comment'),
            'sortable' => false,
            'filter'   => false,
            'align'    => 'left'
        ));
        
        $this->addColumn('created_at', array(
            'type'     => 'datetime',
            'index'    => 'created_at',
            'header'   => Mage::helper('rackpoint')->__('Created At'),
            'sortable' => false,
            'align'    => 'left',
            'html_decorators' => 'nobr',
        ));

        return parent::_prepareColumns();
    }

    /**
     * Return grid url for ajax actions
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('adminhtml/point_customer/historyGrid', array('customer_id' => $this->getCustomerId()));
    }
}
