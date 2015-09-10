<?php

/**
 * @license http://opensource.org/licenses/osl-3.0.php OSL-3.0
 * @author Yoshihisa AMAKATA <amakata@flatz.jp>
 * @copyright (c) 2013, FLATz Inc.
 */
class FLATz_Base_Block_Adminhtml_Tag_Customer_Grid extends Mage_Adminhtml_Block_Tag_Customer_Grid {

    /**
     * 
     * @return \FLATz_Base_Block_Adminhtml_Tag_Customer_Grid
     */
    protected function _prepareColumns() {
        $this->addColumn('customer_id', array(
            'header' => Mage::helper('tag')->__('ID'),
            'width' => 50,
            'align' => 'right',
            'index' => 'entity_id',
        ));

        $this->addColumn('lastname', array(
            'header' => Mage::helper('tag')->__('Last Name'),
            'index' => 'lastname',
        ));

        $this->addColumn('firstname', array(
            'header' => Mage::helper('tag')->__('First Name'),
            'index' => 'firstname',
        ));

        $this->addColumn('product', array(
            'header' => Mage::helper('tag')->__('Product Name'),
            'filter' => false,
            'sortable' => false,
            'index' => 'product',
        ));

        $this->addColumn('product_sku', array(
            'header' => Mage::helper('tag')->__('Product SKU'),
            'filter' => false,
            'sortable' => false,
            'width' => 50,
            'align' => 'right',
            'index' => 'product_sku',
        ));
        $this->sortColumnsByOrder();
        return $this;
    }

}
