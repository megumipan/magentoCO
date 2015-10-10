<?php

/**
 * @license http://opensource.org/licenses/osl-3.0.php OSL-3.0
 * @author Yoshihisa AMAKATA <amakata@flatz.jp>
 * @copyright (c) 2013, FLATz Inc.
 */
class FLATz_Base_Block_Adminhtml_Report_Tag_Customer_Grid extends Mage_Adminhtml_Block_Report_Tag_Customer_Grid {

    /**
     * コンストラクタ
     */
    public function __construct() {
        parent::__construct();
    }

    /**
     * 
     * @return \FLATz_Base_Block_Adminhtml_Report_Tag_Customer_Grid
     */
    protected function _prepareColumns() {
        parent::_prepareColumns();
        if (Mage::app()->getLocale()->getLocaleCode() != 'ja_JP') {
            return $this;
        }
        $this->_columns = array();
        $this->addColumn('entity_id', array(
            'header' => Mage::helper('reports')->__('ID'),
            'width' => '50px',
            'align' => 'right',
            'index' => 'entity_id'
        ));

        $this->addColumn('lastname', array(
            'header' => Mage::helper('reports')->__('Last Name'),
            'index' => 'lastname'
        ));

        $this->addColumn('firstname', array(
            'header' => Mage::helper('reports')->__('First Name'),
            'index' => 'firstname'
        ));

        $this->addColumn('taged', array(
            'header' => Mage::helper('reports')->__('Total Tags'),
            'width' => '50px',
            'align' => 'right',
            'index' => 'taged'
        ));

        $this->addColumn('action', array(
            'header' => Mage::helper('catalog')->__('Action'),
            'width' => '100%',
            'type' => 'action',
            'getter' => 'getId',
            'actions' => array(
                array(
                    'caption' => Mage::helper('catalog')->__('Show Tags'),
                    'url' => array(
                        'base' => '*/*/customerDetail'
                    ),
                    'field' => 'id'
                )
            ),
            'is_system' => true,
            'filter' => false,
            'sortable' => false,
            'index' => 'stores',
        ));

        $this->setFilterVisibility(false);

        $this->addExportType('*/*/exportCustomerCsv', Mage::helper('reports')->__('CSV'));
        $this->addExportType('*/*/exportCustomerExcel', Mage::helper('reports')->__('Excel'));

        return $this;
    }

}