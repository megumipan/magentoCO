<?php
/*
 * Irvine Systems Shipping Japan Sgw
 *
 * NOTICE OF LICENSE
 * This source file is subject to the Revised BSD License which is bundled with
 * this package in the files LICENSE.txt and LICENSE.html.
 *
 * @category	Magento Shipping Extension
 * @package		IrvineSystems_Sagawa
 * @author Irvine Systems, Inc.
 * @copyright Copyright (c) 2015, Irvine Systems, Inc. (http://irvinesystems.co.jp/)
 * @license     Revised BSD License
 */

class IrvineSystems_Sagawa_Block_Adminhtml_Parcels_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
   /**
    * Block constructor, prepare Grid params
    *
    */
	public function __construct()
	{
		// Construct Parent Grid
		parent::__construct();

        // Set Grid Proprietaries
		$this->setId('slipsGrid');
		$this->setDefaultSort('slip_id');
		$this->setDefaultDir('DESC');
		$this->setSaveParametersInSession(true);
	}

	/* 
	 * Prepare Data Collection for rules grid
	 * 
     * @return Mage_Adminhtml_Block_Widget_Grid
	*/
	protected function _prepareCollection()
	{
        // Set Default Status
		$status = 'processing';
		// Prepare data Collection
		$collection = Mage::getResourceModel('sagawa/slips_collection')->addOrderStatusFilter($status)->addParcelFilter();
        // Set the Collection
		$this->setCollection($collection);
        // Prepare Parent Collection
		return parent::_prepareCollection();
	}

    /**
     * Prepare columns for rules grid
     *
     * @return Mage_Adminhtml_Block_Widget_Grid
     */
	protected function _prepareColumns()
	{
        // Set Points Model
		$model = Mage::getModel('sagawa/slips');
        // Set Helper Model
        $helper = Mage::helper('sagawa');

		// Set Main Id Column
		$this->addColumn('slip_id', array(
			'header'	=> $helper->__('ID'),
			'align'		=>'right',
			'width'		=> '50px',
			'index'		=> 'slip_id',
		));

		// Set Order Number Column
		$this->addColumn('order_id', array(
			'header'	=> $helper->__('Order Number'),
			'align'		=>'right',
			'index'		=> 'order_id',
		));

/*
		// Set Tracking Number Column
		$this->addColumn('tracking_number', array(
			'header'	=> $helper->__('Tracking Number'),
			'align'		=>'right',
			'index'		=> 'tracking_number',
		));

		// Set Address Book Code Column
		$this->addColumn('customer_address_code', array(
			'header'	=> $helper->__('Address Book Code'),
			'align'		=>'left',
			'index'		=> 'customer_address_code',
		));

		// Set Customer Telephone Column
		$this->addColumn('customer_tel', array(
			'header'	=> $helper->__('Customer Telephone'),
			'align'		=> 'left',
			'index'		=> 'customer_tel',
		));

		// Set Customer Post Code Column
		$this->addColumn('customer_postcode', array(
			'header'	=> $helper->__('Customer Post Code'),
			'align'		=>'left',
			'index'		=> 'customer_postcode',
		));

		// Set Customer Address (1) Column
		$this->addColumn('customer_address_1', array(
			'header'	=> $helper->__('Customer Address (1)'),
			'align'		=> 'left',
			'index'		=> 'customer_address_1',
		));

		// Set Customer Address (2) Column
		$this->addColumn('customer_address_2', array(
			'header'	=> $helper->__('Customer Address (2)'),
			'align'		=> 'left',
			'index'		=> 'customer_address_2',
		));

		// Set Customer Address (3) Column
		$this->addColumn('customer_address_3', array(
			'header'	=> $helper->__('Customer Address (3)'),
			'align'		=> 'left',
			'index'		=> 'customer_address_3',
		));
*/

		// Set Customer Full Name Column
		$this->addColumn('customer_name', array(
			'header'	=> $helper->__('Customer Full Name'),
			'align'		=> 'left',
			'index'		=> 'customer_name',
		));

/*
		// Set Customer Full Name (kana) Column
		$this->addColumn('customer_namekana', array(
			'header'	=> $helper->__('Customer Full Name (kana)'),
			'align'		=> 'left',
			'index'		=> 'customer_namekana',
		));

		// Set Customer Member ID Column
		$this->addColumn('customer_memberid', array(
			'header'	=> $helper->__('Customer Member ID'),
			'align'		=> 'left',
			'index'		=> 'customer_memberid',
		));

		// Set Customer ID Column
		$this->addColumn('customer_id', array(
			'header'	=> $helper->__('Customer ID'),
			'align'		=> 'left',
			'index'		=> 'customer_id',
		));

		// Set Store Contact Name Column
		$this->addColumn('store_contact', array(
			'header'	=> $helper->__('Store Contact Name'),
			'align'		=> 'left',
			'index'		=> 'store_contact',
		));

		// Set Shipper Telephone Column
		$this->addColumn('shipper_tel', array(
			'header'	=> $helper->__('Shipper Telephone'),
			'align'		=> 'left',
			'index'		=> 'shipper_tel',
		));

		// Set Store Telephone Column
		$this->addColumn('store_tel', array(
			'header'	=> $helper->__('Store Telephone'),
			'align'		=> 'left',
			'index'		=> 'store_tel',
		));

		// Set Store Postcode Column
		$this->addColumn('store_postcode', array(
			'header'	=> $helper->__('Store Postcode'),
			'align'		=> 'left',
			'index'		=> 'store_postcode',
		));

		// Set Store Address 1 Column
		$this->addColumn('store_address_1', array(
			'header'	=> $helper->__('Store Address (1)'),
			'align'		=> 'left',
			'index'		=> 'store_address_1',
		));

		// Set Store Address 2 Column
		$this->addColumn('store_address_2', array(
			'header'	=> $helper->__('Store Address (2)'),
			'align'		=> 'left',
			'index'		=> 'store_address_2',
		));

		// Set Store Name Column
		$this->addColumn('store_name', array(
			'header'	=> $helper->__('Store Name'),
			'align'		=> 'left',
			'index'		=> 'store_name',
		));

		// Set Store Name (Kana) Column
		$this->addColumn('store_namekana', array(
			'header'	=> $helper->__('Store Name (Kana)'),
			'align'		=> 'left',
			'index'		=> 'store_namekana',
		));
*/

		// Set Packing Code Column
		$this->addColumn('packing_code', array(
			'header'	=> $helper->__('Packing Type'),
			'align'		=> 'left',
			'index'		=> 'packing_code',
            'type'      => 'options',
            'options'   => $model->getPkgCodeTypes(),
		));

/*
		// Set 1st Product Name Column
		$this->addColumn('product_name_1', array(
			'header'	=> $helper->__('1st Product Name'),
			'align'		=> 'left',
			'index'		=> 'product_name_1',
		));

		// Set 2nd Product Name Column
		$this->addColumn('product_name_2', array(
			'header'	=> $helper->__('2nd Product Name'),
			'align'		=> 'left',
			'index'		=> 'product_name_2',
		));

		// Set 3rd Product Name Column
		$this->addColumn('product_name_3', array(
			'header'	=> $helper->__('3rd Product Name'),
			'align'		=> 'left',
			'index'		=> 'product_name_3',
		));

		// Set 4th Product Name Column
		$this->addColumn('product_name_4', array(
			'header'	=> $helper->__('4th Product Name'),
			'align'		=> 'left',
			'index'		=> 'product_name_4',
		));

		// Set 5th Product Name Column
		$this->addColumn('product_name_5', array(
			'header'	=> $helper->__('5th Product Name'),
			'align'		=> 'left',
			'index'		=> 'product_name_5',
		));

		// Set Total Packages Number Name Column
		$this->addColumn('packages_number', array(
			'header'	=> $helper->__('Total Packages Number'),
			'align'		=> 'left',
			'index'		=> 'packages_number',
		));
*/

		// Set Shipping Method Column
		$this->addColumn('ship_method', array(
			'header'	=> $helper->__('Shipping Method'),
			'align'		=> 'left',
			'index'		=> 'ship_method',
            'type'      => 'options',
            'options'   => $model->getShpMethodCodeTypes(),
		));

		// Set Fragile Product Column
		$this->addColumn('fragile_status', array(
			'header'	=> $helper->__('Fragile Products'),
			'align'		=> 'center',
			'index'		=> 'fragile_status',
            'type'      => 'options',
            'options'   => $model->getBoolTypes(),
		));

		// Set Valuable Products Column
		$this->addColumn('valuables_status', array(
			'header'	=> $helper->__('Valuable Products'),
			'align'		=> 'center',
			'index'		=> 'valuables_status',
            'type'      => 'options',
            'options'   => $model->getBoolTypes(),
		));

		// Set Up Side Specified Column
		$this->addColumn('side_status', array(
			'header'	=> $helper->__('Up Side Specified'),
			'align'		=> 'center',
			'index'		=> 'side_status',
            'type'      => 'options',
            'options'   => $model->getBoolTypes(),
		));

		// Set Cooling Shipment Column
		$this->addColumn('cooling_shipment', array(
			'header'	=> $helper->__('Cooling Shipment'),
			'align'		=> 'center',
			'index'		=> 'cooling_shipment',
            'type'      => 'options',
            'options'   => $model->getCoolShipCodeTypes(),
		));

		// Set Delivery Date Column
		$this->addColumn('delivery_date', array(
			'header'	=> $helper->__('Delivery Date'),
			'align'		=>'right',
			'index'		=> 'delivery_date',
		));

		/*
		//Get the type mapping array acording to admin settings
		switch (Mage::getStoreConfig('sagawa/slips/ship_time_class')) {
		    case 0:
		        $timeOptions = $model->getTimeZoneShortTypes();
		        break;
		    default:
		        $timeOptions = $model->getTimeZoneLongTypes();
		        break;
		}
		*/
		// Set Delivery Time Column
		$this->addColumn('delivery_time', array(
			'header'	=> $helper->__('Delivery Time'),
			'align'		=>'right',
			'index'		=> 'delivery_time',
            'type'      => 'options',
            'options'   => $model->getTimeZoneFullTypes(),
            //'options'   => $timeOptions,
		));

		// Set Delivery Time Column
		$this->addColumn('delivery_comment', array(
			'header'	=> $helper->__('Delivery Comment'),
			'align'		=>'right',
			'index'		=> 'delivery_comment',
            'type'      => 'options',
            'options'   => $timeOptions,
		));

/*
		// Set Delivery Time (min) Column
		$this->addColumn('delivery_time_min', array(
			'header'	=> $helper->__('Delivery Time (min)'),
			'align'		=>'right',
			'index'		=> 'delivery_time_min',
		));

		// Set Cash on Delivery Amount Column
		$this->addColumn('cod_amount', array(
			'header'	=> $helper->__('Cash on Delivery Amount'),
			'align'		=>'right',
			'index'		=> 'cod_amount',
		));

		// Set Tax Amount Column
		$this->addColumn('tax_amount', array(
			'header'	=> $helper->__('Tax Amount'),
			'align'		=>'right',
			'index'		=> 'tax_amount',
		));

		// Set Cash on Delivery Payment Method Column
		$this->addColumn('cod_method', array(
			'header'	=> $helper->__('Cash on Delivery Payment Method'),
			'align'		=>'right',
			'index'		=> 'cod_method',
            'type'      => 'options',
            'options'   => $model->getCodPaymentMethodTypes(),
		));

		// Set Ensured Amount Column
		$this->addColumn('ensured_amount', array(
			'header'	=> $helper->__('Ensured Amount'),
			'align'		=>'right',
			'index'		=> 'ensured_amount',
		));

		// Set Ensured Amount (Printed) Column
		$this->addColumn('ensured_amount_printed', array(
			'header'	=> $helper->__('Ensured Amount (Printed)'),
			'align'		=>'right',
			'index'		=> 'ensured_amount_printed',
            'type'      => 'options',
            'options'   => $model->getEnsuredPrintTypes(),
		));
*/

		// Set 1st Service Code Column
		$this->addColumn('service_code_1', array(
			'header'	=> $helper->__('1st Service Code'),
			'align'		=>'center',
			'index'		=> 'service_code_1',
            'type'      => 'options',
            'options'   => $model->getServiceCodesTypes(),
		));

		// Set 2nd Service Code Column
		$this->addColumn('service_code_2', array(
			'header'	=> $helper->__('2nd Service Code'),
			'align'		=>'center',
			'index'		=> 'service_code_2',
            'type'      => 'options',
            'options'   => $model->getServiceCodesTypes(),
		));

		// Set 3rd Service Code Column
		$this->addColumn('service_code_3', array(
			'header'	=> $helper->__('3rd Service Code'),
			'align'		=>'center',
			'index'		=> 'service_code_3',
            'type'      => 'options',
            'options'   => $model->getServiceCodesTypes(),
		));

		// Set Delivery Type Column
		$this->addColumn('delivery_type', array(
			'header'	=> $helper->__('Delivery Type'),
			'align'		=>'left',
			'index'		=> 'delivery_type',
            'type'      => 'options',
            'options'   => $model->getDeliveryTypes(),
		));

/*
		// Set SRC Classification Column
		$this->addColumn('src_class', array(
			'header'	=> $helper->__('SRC Classification'),
			'align'		=>'left',
			'index'		=> 'src_class',
            'type'      => 'options',
            'options'   => $model->getSrcClassTypes(),
		));

		// Set Branch Code Column
		$this->addColumn('branc_code', array(
			'header'	=> $helper->__('Branch Code'),
			'align'		=>'left',
			'index'		=> 'branc_code',
		));

		// Set Delivery Payment Source Column
		$this->addColumn('payment_source', array(
			'header'	=> $helper->__('Delivery Payment Source'),
			'align'		=>'left',
			'index'		=> 'payment_source',
            'type'      => 'options',
            'options'   => $model->getDelPaySourceTypes(),
		));
*/

		// Set Export Types
		$this->addExportType('*/*/exportCsv', $helper->__('CSV'));
		$this->addExportType('*/*/exportXml', $helper->__('XML'));
		
		// Prepare parent Column
		return parent::_prepareColumns();
	}

    /**
     * Prepare grid URL
     *
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('id' => $row->getSlipId()));
    }
}