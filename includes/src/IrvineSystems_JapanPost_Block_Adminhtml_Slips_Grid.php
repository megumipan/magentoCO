<?php
/*
 * Irvine Systems Shipping Japan Jp
 *
 * NOTICE OF LICENSE
 * This source file is subject to the Revised BSD License which is bundled with
 * this package in the files LICENSE.txt and LICENSE.html.
 *
 * @category	Magento Shipping Extension
 * @package		IrvineSystems_JapanPost
 * @author Irvine Systems, Inc.
 * @copyright Copyright (c) 2015, Irvine Systems, Inc. (http://irvinesystems.co.jp/)
 * @license     Revised BSD License
 */

class IrvineSystems_JapanPost_Block_Adminhtml_Slips_Grid extends Mage_Adminhtml_Block_Widget_Grid
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
		$collection = Mage::getResourceModel('japanpost/slips_collection')->addOrderStatusFilter($status);
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
		$model = Mage::getModel('japanpost/slips');
        // Set Helper Model
        $helper = Mage::helper('japanpost');

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

		// Set Customer Post Code Column
		$this->addColumn('customer_postcode', array(
			'header'	=> $helper->__('Customer Post Code'),
			'align'		=> 'left',
			'index'		=> 'customer_postcode',
		));

		// Set Customer Address Column
		$this->addColumn('customer_address', array(
			'header'	=> $helper->__('Customer Address'),
			'align'		=> 'left',
			'index'		=> 'customer_address',
		));

		// Set Customer Full Name Column
		$this->addColumn('customer_name', array(
			'header'	=> $helper->__('Customer Full Name'),
			'align'		=> 'left',
			'index'		=> 'customer_name',
		));

		// Set Customer Full Name (kana) Column
		$this->addColumn('customer_namekana', array(
			'header'	=> $helper->__('Customer Full Name (kana)'),
			'align'		=> 'left',
			'index'		=> 'customer_namekana',
		));

		// Set Customer Prefix Column
		$this->addColumn('customer_prefix', array(
			'header'	=> $helper->__('Customer Prefix'),
			'align'		=> 'left',
			'index'		=> 'customer_prefix',
            'type'      => 'options',
            'options'   => $model->getPrefixTypes(),
		));

		// Set Customer Telephone Column
		$this->addColumn('customer_tel', array(
			'header'	=> $helper->__('Customer Telephone'),
			'align'		=> 'left',
			'index'		=> 'customer_tel',
		));

		// Set Customer Email Column
		$this->addColumn('customer_email', array(
			'header'	=> $helper->__('Customer Email'),
			'align'		=> 'left',
			'index'		=> 'customer_email',
		));

		// Set Customer ID Column
		$this->addColumn('customer_id', array(
			'header'	=> $helper->__('Customer ID'),
			'align'		=> 'left',
			'index'		=> 'customer_id',
		));

		// Set Store Postcode Column
		$this->addColumn('store_postcode', array(
			'header'	=> $helper->__('Store Postcode'),
			'align'		=> 'left',
			'index'		=> 'store_postcode',
		));

		// Set Store Postcode Column
		$this->addColumn('store_address', array(
			'header'	=> $helper->__('Store Address'),
			'align'		=> 'left',
			'index'		=> 'store_address',
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

		// Set Store Prefix Column
		$this->addColumn('store_prefix', array(
			'header'	=> $helper->__('Store Prefix'),
			'align'		=> 'left',
			'index'		=> 'store_prefix',
            'type'      => 'options',
            'options'   => $model->getPrefixTypes(),
		));

		// Set Store Telephone Column
		$this->addColumn('store_tel', array(
			'header'	=> $helper->__('Store Telephone'),
			'align'		=> 'left',
			'index'		=> 'store_tel',
		));

		// Set Store Telephone Column
		$this->addColumn('store_email', array(
			'header'	=> $helper->__('Store Email'),
			'align'		=> 'left',
			'index'		=> 'store_email',
		));

		// Set Store Member Number Column
		$this->addColumn('store_memberid', array(
			'header'	=> $helper->__('Store Member Number'),
			'align'		=> 'left',
			'index'		=> 'store_memberid',
		));
*/

		// Set Has Fragile Column
		$this->addColumn('fragile_status', array(
			'header'	=> $helper->__('Has Fragile'),
			'align'		=> 'center',
			'index'		=> 'fragile_status',
            'type'      => 'options',
            'options'   => $model->getBoolTypes(),
		));

		// Set Has Creature Column
		$this->addColumn('creature_status', array(
			'header'	=> $helper->__('Has Creature'),
			'align'		=> 'center',
			'index'		=> 'creature_status',
            'type'      => 'options',
            'options'   => $model->getBoolTypes(),
		));

		// Set Has Glass Column
		$this->addColumn('glass_status', array(
			'header'	=> $helper->__('Has Glass'),
			'align'		=> 'center',
			'index'		=> 'glass_status',
            'type'      => 'options',
            'options'   => $model->getBoolTypes(),
		));

		// Set Don't Rotate Box Column
		$this->addColumn('side_status', array(
			'header'	=> $helper->__("Can't Rotate Box"),
			'align'		=> 'center',
			'index'		=> 'side_status',
            'type'      => 'options',
            'options'   => $model->getBoolTypes(),
		));

		// Set Don't Put Weight Column
		$this->addColumn('weight_status', array(
			'header'	=> $helper->__("Can't Put Weight"),
			'align'		=> 'center',
			'index'		=> 'weight_status',
            'type'      => 'options',
            'options'   => $model->getBoolTypes(),
		));

		// Set Package Full Weight Column
		$this->addColumn('package_weight', array(
			'header'	=> $helper->__('Package Weight (g)'),
			'align'		=> 'right',
			'index'		=> 'package_weight',
		));

		// Set Package Full Size Column
		$this->addColumn('package_size', array(
			'header'	=> $helper->__('Package Size (mm)'),
			'align'		=>'right',
			'index'		=> 'package_size',
		));

/*
		// Set Delivery Mode Column
		$this->addColumn('delivery_mode', array(
			'header'	=> $helper->__("Delivery Mode"),
			'align'		=> 'left',
			'index'		=> 'delivery_mode',
            'type'      => 'options',
            'options'   => $model->getDelModTypes(),
		));
*/

		// Set Delivery Date Column
		$this->addColumn('delivery_date', array(
			'header'	=> $helper->__('Delivery Date'),
			'align'		=> 'right',
			'type'		=> 'date',
			'format'    => 'Y/MM/dd',
			'index'		=> 'delivery_date',
		));

		// Set Delivery Time Column
		$this->addColumn('delivery_time', array(
			'header'	=> $helper->__('Delivery Time'),
			'align'		=>'right',
			'index'		=> 'delivery_time',
		));

/*
		// Set Post Notification Column
		$this->addColumn('notification_post', array(
			'header'	=> $helper->__('Post Notification'),
			'align'		=> 'center',
			'index'		=> 'notification_post',
            'type'      => 'options',
            'options'   => $model->getBoolTypes(),
		));

		// Set Email Notification Column
		$this->addColumn('notification_email', array(
			'header'	=> $helper->__('Email Notification'),
			'align'		=> 'center',
			'index'		=> 'notification_email',
            'type'      => 'options',
            'options'   => $model->getBoolTypes(),
		));

		// Set Mail Class Column
		$this->addColumn('mail_class', array(
			'header'	=> $helper->__('Mail Class'),
			'align'		=> 'left',
			'index'		=> 'mail_class',
            'type'      => 'options',
            'options'   => $model->getMailClassTypes(),
		));
*/

		// Set Cash on Delivery Column
		$this->addColumn('cod_status', array(
			'header'	=> $helper->__('Cash on Delivery'),
			'align'		=> 'center',
			'index'		=> 'cod_status',
            'type'      => 'options',
            'options'   => $model->getBoolTypes(),
		));

/*
		// Set Cash on Delivery Amount Column
		$this->addColumn('cod_amount', array(
			'header'	=> $helper->__('Cash on Delivery Amount'),
			'align'		=>'right',
			'index'		=> 'cod_amount',
		));
*/

		// Set Delivery Services Column
		$this->addColumn('ship_service', array(
			'header'	=> $helper->__('Delivery Services'),
			'align'		=> 'left',
			'index'		=> 'ship_service',
            'type'      => 'options',
            'options'   => $model->getDelSerTypes(),
		));

/*
		// Set Ensured Amount Column
		$this->addColumn('ensured_amount', array(
			'header'	=> $helper->__('Ensured Amount'),
			'align'		=>'right',
			'index'		=> 'ensured_amount',
		));

		// Set Delivery Payment Source Column
		$this->addColumn('payment_source', array(
			'header'	=> $helper->__('Delivery Payment Source'),
			'align'		=> 'left',
			'index'		=> 'payment_source',
            'type'      => 'options',
            'options'   => $model->getDelPaySourceTypes(),
		));
*/

		// Set Cooling Shipment Column
		$this->addColumn('ship_cooler', array(
			'header'	=> $helper->__('Cooling Shipment Required'),
			'align'		=> 'left',
			'index'		=> 'ship_cooler',
            'type'      => 'options',
            'options'   => $model->getCoolShipTypes(),
		));

/*
		// Set Delivery Types Column
		$this->addColumn('delivery_type', array(
			'header'	=> $helper->__('Delivery Type'),
			'align'		=> 'left',
			'index'		=> 'delivery_type',
            'type'      => 'options',
            'options'   => $model->getDelTypes(),
		));

		// Set Discount Types Column
		$this->addColumn('discount_type', array(
			'header'	=> $helper->__('Discount Type'),
			'align'		=> 'left',
			'index'		=> 'discount_type',
            'type'      => 'options',
            'options'   => $model->getDiscountTypes(),
		));
*/

		// Set Delivery Comment Column
		$this->addColumn('delivery_comment', array(
			'header'	=> $helper->__('Delivery Comment'),
			'align'		=> 'left',
			'index'		=> 'delivery_comment',
		));

/*
		// Set Free Field Column
		$this->addColumn('free_field2', array(
			'header'	=> $helper->__('Free Field'),
			'align'		=> 'left',
			'index'		=> 'free_field2',
		));

		// Set Free Field Column
		$this->addColumn('free_field3', array(
			'header'	=> $helper->__('Free Field'),
			'align'		=> 'left',
			'index'		=> 'free_field3',
		));

		// Set Free Field Column
		$this->addColumn('free_field4', array(
			'header'	=> $helper->__('Free Field'),
			'align'		=> 'left',
			'index'		=> 'free_field4',
		));

		// Set Free Field Column
		$this->addColumn('free_field5', array(
			'header'	=> $helper->__('Free Field'),
			'align'		=> 'left',
			'index'		=> 'free_field5',
		));

		// Set Taxable Types Column
		$this->addColumn('taxable', array(
			'header'	=> $helper->__('Taxable'),
			'align'		=> 'center',
			'index'		=> 'taxable',
            'type'      => 'options',
            'options'   => $model->getTaxTypes(),
		));

		// Set Sort Code Column
		$this->addColumn('sort_code', array(
			'header'	=> $helper->__('Sort Code'),
			'align'		=>'right',
			'index'		=> 'sort_code',
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