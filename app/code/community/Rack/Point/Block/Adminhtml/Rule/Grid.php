<?php

class Rack_Point_Block_Adminhtml_Rule_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('point_rule_grid');
        $this->setDefaultSort('name');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel('rackpoint/catalog_rule')
            ->getResourceCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('rule_id', array(
            'header'    => Mage::helper('rackpoint')->__('ID'),
            'align'     =>'right',
            'width'     => '50px',
            'index'     => 'rule_id',
        ));

        $this->addColumn('name', array(
            'header'    => Mage::helper('rackpoint')->__('Rule Name'),
            'align'     =>'left',
            'index'     => 'name',
        ));
        
        $this->addColumn('simple_action', array(
            'header'    => Mage::helper('rackpoint')->__('Calculate Type'),
            'align'     =>'left',
            'index'     => 'simple_action',
            'type'   => 'options',
            'options' => array(
                    'by_percent' => Mage::helper('rackpoint')->__('By percent'),
                    'by_fixed' => Mage::helper('rackpoint')->__('By fixed'),
                ),

        ));
        
        $this->addColumn('point_amount', array(
            'header'    => Mage::helper('rackpoint')->__('Point Amount'),
            'align'     =>'left',
            'type'      => 'number',
            'index'     => 'point_amount',
        ));
        
        $this->addColumn('sort_order', array(
            'header'    => Mage::helper('rackpoint')->__('Priority'),
            'align'     =>'left',
            'type'      => 'number',
            'index'     => 'sort_order',
        ));

        $this->addColumn('from_date', array(
            'header'    => Mage::helper('rackpoint')->__('Date Start'),
            'align'     => 'left',
            'width'     => '120px',
            'type'      => 'date',
            'index'     => 'from_date',
        ));

        $this->addColumn('to_date', array(
            'header'    => Mage::helper('rackpoint')->__('Date Expire'),
            'align'     => 'left',
            'width'     => '120px',
            'type'      => 'date',
            'default'   => '--',
            'index'     => 'to_date',
        ));

        $this->addColumn('is_active', array(
            'header'    => Mage::helper('rackpoint')->__('Status'),
            'align'     => 'left',
            'width'     => '80px',
            'index'     => 'is_active',
            'type'      => 'options',
            'options'   => array(
                1 => Mage::helper('rackpoint')->__('Active'),
                0 => Mage::helper('rackpoint')->__('Inactive'),
            ),
        ));

        return parent::_prepareColumns();
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('id' => $row->getRuleId()));
    }

}
