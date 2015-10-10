<?php
/**
 * @license http://opensource.org/licenses/osl-3.0.php OSL-3.0
 * @author Yoshihisa AMAKATA <amakata@flatz.jp>
 * @copyright (c) 2013, FLATz Inc.
 */
class FLATz_Base_Block_Adminhtml_Directory_Grid_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * コンストラクタ
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('directoryGrid');
        $this->setDefaultSort('id');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
    }

    /**
     * 
     * @return \FLATz_Base_Block_Adminhtml_Directory_Grid_Grid
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getModel('flatz_base/postcode')->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }
    /**
     * 
     * @return \FLATz_Base_Block_Adminhtml_Directory_Grid_Grid
     */
    protected function _prepareColumns()
    {
        $this->addColumn('id', array(
            'header'    => Mage::helper('flatz_base')->__('ID'),
            'align'     =>'right',
            'width'     => '30px',
            'index'     => 'currency_id',
         ));

        $this->addColumn('currency code', array(
            'header'    => Mage::helper('flatz_base')->__('Currency Code'),
            'align'     =>'right',
            'width'     => '50px',
            'index'     => 'currency_code',
        ));

        $this->addColumn('precision', array(
            'header'    => Mage::helper('flatz_base')->__('Precision'),
            'align'     => 'left',
            'width'     => '150px',
            'index'     => 'precision',
        ));
        
        $this->addColumn('marker_position', array(
            'header'    => Mage::helper('flatz_base')->__('Marker Position'),
            'align'     => 'left',
            'width'     => '50px',
            'index'     => 'marker_position',
        ));
      
        $this->addColumn('area', array(
            'header'    => Mage::helper('flatz_base')->__('Area'),
            'align'     => 'left',
            'width'     => '250px',
            'index'     => 'area',
        ));
      
        $this->addColumn('action',
            array(
                'header'    =>  Mage::helper('flatz_base')->__('Action'),
                'width'     => '50',
                'type'      => 'action',
                'getter'    => 'getId',
                'actions'   => array(
                    array(
                        'caption'   => Mage::helper('flatz_base')->__('Edit'),
                        'url'       => array('base'=> '*/*/edit'),
                        'field'     => 'id'
                    )
                ),
                'filter'    => false,
                'sortable'  => false,
                'index'     => 'stores',
                'is_system' => true,
        ));

        //$this->addExportType('*/*/exportCsv', Mage::helper('flatz_base')->__('CSV'));
        //$this->addExportType('*/*/exportXml', Mage::helper('flatz_base')->__('XML'));
        
      return parent::_prepareColumns();
    }
    
    /**
     * 
     * @return \FLATz_Base_Block_Adminhtml_Directory_Grid_Grid
     */
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('id');
        $this->getMassactionBlock()->setFormFieldName('currency_id');

        $this->getMassactionBlock()->addItem('delete', array(
             'label'    => Mage::helper('flatz_base')->__('Delete'),
             'url'      => $this->getUrl('*/*/massDelete'),
             'confirm'  => Mage::helper('flatz_base')->__('Are you sure?')
        ));

        return $this;
    }
    
    /**
     * 
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', array('_current'=>true));
    }

    /**
     * 
     * @param  $row
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }
}