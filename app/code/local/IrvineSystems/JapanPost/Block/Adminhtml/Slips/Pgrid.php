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

class IrvineSystems_JapanPost_Block_Adminhtml_Slips_Pgrid extends Mage_Adminhtml_Block_Widget_Grid
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

/*
		// Set Main Id Column
		$this->addColumn('slip_id', array(
			'header'	=> $helper->__('ID'),
			'align'		=>'right',
			'width'		=> '50px',
			'index'		=> 'slip_id',
		));
*/

		// Set ID_001 Column
		$this->addColumn('id_001', array(
			'header'	=> $helper->__('ID_001'),
		));

		// Set ID_002 Column
		$this->addColumn('id_002', array(
			'header'	=> $helper->__('ID_002'),
		));

		// Set ID_003 Column
		$this->addColumn('id_003', array(
			'header'	=> $helper->__('ID_003'),
		));

		// Set ID_004 Column
		$this->addColumn('id_004', array(
			'header'	=> $helper->__('ID_004'),
		));

		// Set ID_005 Column
		$this->addColumn('id_005', array(
			'header'	=> $helper->__('ID_005'),
		));

		// Set ID_006 Column
		$this->addColumn('id_006', array(
			'header'	=> $helper->__('ID_006'),
		));

		// Set ID_007 Column
		$this->addColumn('id_007', array(
			'header'	=> $helper->__('ID_007'),
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

		// Set ID_010 Column
		$this->addColumn('id_010', array(
			'header'	=> $helper->__('ID_010'),
		));

		// Set ID_011 Column
		$this->addColumn('id_011', array(
			'header'	=> $helper->__('ID_011'),
		));

		// Set ID_012 Column
		$this->addColumn('id_012', array(
			'header'	=> $helper->__('ID_012'),
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

		// Set ID_019 Column
		$this->addColumn('id_019', array(
			'header'	=> $helper->__('ID_019'),
		));

		// Set ID_020 Column
		$this->addColumn('id_020', array(
			'header'	=> $helper->__('ID_020'),
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

		// Set ID_023 Column
		$this->addColumn('id_023', array(
			'header'	=> $helper->__('ID_023'),
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
		));

		// Set Store Telephone Column
		$this->addColumn('store_tel', array(
			'header'	=> $helper->__('Store Telephone'),
			'align'		=> 'left',
			'index'		=> 'store_tel',
		));

		// Set ID_028 Column
		$this->addColumn('id_028', array(
			'header'	=> $helper->__('ID_028'),
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

		// Set ID_031 Column
		$this->addColumn('id_031', array(
			'header'	=> $helper->__('ID_031'),
		));

		// Set ID_032 Column
		$this->addColumn('id_032', array(
			'header'	=> $helper->__('ID_032'),
		));

		// Set Mail Class Column
		$this->addColumn('mail_class', array(
			'header'	=> $helper->__('Mail Class'),
			'align'		=> 'left',
			'index'		=> 'mail_class',
		));

		// Set ID_034 Column
		$this->addColumn('id_034', array(
			'header'	=> $helper->__('ID_034'),
		));

		// Set Post Notification Column
		$this->addColumn('notification_post', array(
			'header'	=> $helper->__('Post Notification'),
			'align'		=> 'center',
			'index'		=> 'notification_post',
		));

		// Set Email Notification Column
		$this->addColumn('notification_email', array(
			'header'	=> $helper->__('Email Notification'),
			'align'		=> 'center',
			'index'		=> 'notification_email',
		));

		// Set Product Number Column
		$this->addColumn('product_number', array(
			'header'	=> $helper->__('Product Number'),
			'align'		=> 'right',
			'index'		=> 'product_number',
		));

		// Set Product Name Column
		$this->addColumn('product_name', array(
			'header'	=> $helper->__('Product Name'),
			'align'		=> 'right',
			'index'		=> 'product_name',
		));

		// Set ID_039 Column
		$this->addColumn('id_039', array(
			'header'	=> $helper->__('ID_039'),
		));

		// Set Has Fragile Column
		$this->addColumn('fragile_status', array(
			'header'	=> $helper->__('Has Fragile'),
			'align'		=> 'center',
			'index'		=> 'fragile_status',
		));

		// Set Has Creature Column
		$this->addColumn('creature_status', array(
			'header'	=> $helper->__('Has Creature'),
			'align'		=> 'center',
			'index'		=> 'creature_status',
		));

		// Set Has Glass Column
		$this->addColumn('glass_status', array(
			'header'	=> $helper->__('Has Glass'),
			'align'		=> 'center',
			'index'		=> 'glass_status',
		));

		// Set Don't Rotate Box Column
		$this->addColumn('side_status', array(
			'header'	=> $helper->__("Can't Rotate Box"),
			'align'		=> 'center',
			'index'		=> 'side_status',
		));

		// Set ID_044 Column
		$this->addColumn('id_044', array(
			'header'	=> $helper->__('ID_044'),
		));

		// Set Package Full Size Column
		$this->addColumn('package_size', array(
			'header'	=> $helper->__('Package Size (mm)'),
			'align'		=>'right',
			'index'		=> 'package_size',
		));

		// Set Package Full Weight Column
		$this->addColumn('package_weight', array(
			'header'	=> $helper->__('Package Weight (g)'),
			'align'		=> 'right',
			'index'		=> 'package_weight',
		));

		// Set ID_047 Column
		$this->addColumn('id_047', array(
			'header'	=> $helper->__('ID_047'),
		));

		// Set ID_048 Column
		$this->addColumn('id_048', array(
			'header'	=> $helper->__('ID_048'),
		));

		// Set ID_049 Column
		$this->addColumn('id_049', array(
			'header'	=> $helper->__('ID_049'),
		));

		// Set ID_050 Column
		$this->addColumn('id_050', array(
			'header'	=> $helper->__('ID_050'),
		));

		// Set ID_051 Column
		$this->addColumn('id_051', array(
			'header'	=> $helper->__('ID_051'),
		));

		// Set ID_052 Column
		$this->addColumn('id_052', array(
			'header'	=> $helper->__('ID_052'),
		));

		// Set ID_053 Column
		$this->addColumn('id_053', array(
			'header'	=> $helper->__('ID_053'),
		));

		// Set ID_054 Column
		$this->addColumn('id_054', array(
			'header'	=> $helper->__('ID_054'),
		));

		// Set ID_055 Column
		$this->addColumn('id_055', array(
			'header'	=> $helper->__('ID_055'),
		));

		// Set ID_056 Column
		$this->addColumn('id_056', array(
			'header'	=> $helper->__('ID_056'),
		));

		// Set Delivery Mode Column
		$this->addColumn('delivery_mode', array(
			'header'	=> $helper->__("Delivery Mode"),
			'align'		=> 'left',
			'index'		=> 'delivery_mode',
		));

		// Set Delivery Date Column
		$this->addColumn('delivery_date', array(
			'header'	=> $helper->__('Delivery Date'),
			'align'		=>'right',
			'type'		=> 'date',
			'format'    => 'Y/MM/dd',
			'index'		=> 'delivery_date',
		));

		// Set ID_059 Column
		$this->addColumn('id_059', array(
			'header'	=> $helper->__('ID_059'),
		));

		// Set Delivery Time Column
		$this->addColumn('delivery_time', array(
			'header'	=> $helper->__('Delivery Time'),
			'align'		=>'right',
			'index'		=> 'delivery_time',
		));

		// Set Delivery Services Column
		$this->addColumn('ship_service', array(
			'header'	=> $helper->__('Delivery Services'),
			'align'		=> 'left',
			'index'		=> 'ship_service',
		));

		// Set Cooling Shipment Column
		$this->addColumn('ship_cooler', array(
			'header'	=> $helper->__('Cooling Shipment Required'),
			'align'		=> 'left',
			'index'		=> 'ship_cooler',
		));

		// Set Delivery Payment Source Column
		$this->addColumn('payment_source', array(
			'header'	=> $helper->__('Delivery Payment Source'),
			'align'		=> 'left',
			'index'		=> 'payment_source',
		));

		// Set Delivery Types Column
		$this->addColumn('delivery_type', array(
			'header'	=> $helper->__('Delivery Type'),
			'align'		=> 'left',
			'index'		=> 'delivery_type',
		));

		// Set Discount Types Column
		$this->addColumn('discount_type', array(
			'header'	=> $helper->__('Discount Type'),
			'align'		=> 'left',
			'index'		=> 'discount_type',
		));

		// Set Ensured Amount Column
		$this->addColumn('ensured_amount', array(
			'header'	=> $helper->__('Ensured Amount'),
			'align'		=>'right',
			'index'		=> 'ensured_amount',
		));

		// Set ID_067 Column
		$this->addColumn('id_067', array(
			'header'	=> $helper->__('ID_067'),
		));

		// Set ID_068 Column
		$this->addColumn('id_068', array(
			'header'	=> $helper->__('ID_068'),
		));

		// Set ID_069 Column
		$this->addColumn('id_069', array(
			'header'	=> $helper->__('ID_069'),
		));

		// Set ID_070 Column
		$this->addColumn('id_070', array(
			'header'	=> $helper->__('ID_070'),
		));

		// Set ID_071 Column
		$this->addColumn('id_071', array(
			'header'	=> $helper->__('ID_071'),
		));

		// Set ID_072 Column
		$this->addColumn('id_072', array(
			'header'	=> $helper->__('ID_072'),
		));

		// Set ID_073 Column
		$this->addColumn('id_073', array(
			'header'	=> $helper->__('ID_073'),
		));

		// Set ID_074 Column
		$this->addColumn('id_074', array(
			'header'	=> $helper->__('ID_074'),
		));

		// Set ID_075 Column
		$this->addColumn('id_075', array(
			'header'	=> $helper->__('ID_075'),
		));

		// Set ID_076 Column
		$this->addColumn('id_076', array(
			'header'	=> $helper->__('ID_076'),
		));

		// Set ID_077 Column
		$this->addColumn('id_077', array(
			'header'	=> $helper->__('ID_077'),
		));

		// Set Taxable Types Column
		$this->addColumn('taxable', array(
			'header'	=> $helper->__('Taxable'),
			'align'		=> 'center',
			'index'		=> 'taxable',
		));

		// Set ID_079 Column
		$this->addColumn('id_079', array(
			'header'	=> $helper->__('ID_079'),
		));

		// Set Cash on Delivery Column
		$this->addColumn('cod_status', array(
			'header'	=> $helper->__('Cash on Delivery'),
			'align'		=> 'center',
			'index'		=> 'cod_status',
		));

		// Set ID_081 Column
		$this->addColumn('id_081', array(
			'header'	=> $helper->__('ID_081'),
		));

		// Set Cash on Delivery Amount Column
		$this->addColumn('cod_amount', array(
			'header'	=> $helper->__('Cash on Delivery Amount'),
			'align'		=>'right',
			'index'		=> 'cod_amount',
		));

		// Set ID_083 Column
		$this->addColumn('id_083', array(
			'header'	=> $helper->__('ID_083'),
		));

		// Set ID_084 Column
		$this->addColumn('id_084', array(
			'header'	=> $helper->__('ID_084'),
		));

		// Set ID_085 Column
		$this->addColumn('id_085', array(
			'header'	=> $helper->__('ID_085'),
		));

		// Set ID_086 Column
		$this->addColumn('id_086', array(
			'header'	=> $helper->__('ID_086'),
		));

		// Set ID_087 Column
		$this->addColumn('id_087', array(
			'header'	=> $helper->__('ID_087'),
		));

		// Set ID_088 Column
		$this->addColumn('id_088', array(
			'header'	=> $helper->__('ID_088'),
		));

		// Set ID_089 Column
		$this->addColumn('id_089', array(
			'header'	=> $helper->__('ID_089'),
		));

		// Set ID_090 Column
		$this->addColumn('id_090', array(
			'header'	=> $helper->__('ID_090'),
		));

		// Set ID_091 Column
		$this->addColumn('id_091', array(
			'header'	=> $helper->__('ID_091'),
		));

		// Set ID_092 Column
		$this->addColumn('id_092', array(
			'header'	=> $helper->__('ID_092'),
		));

		// Set ID_093 Column
		$this->addColumn('id_093', array(
			'header'	=> $helper->__('ID_093'),
		));

		// Set ID_094 Column
		$this->addColumn('id_094', array(
			'header'	=> $helper->__('ID_094'),
		));

		// Set ID_095 Column
		$this->addColumn('id_095', array(
			'header'	=> $helper->__('ID_095'),
		));

		// Set ID_096 Column
		$this->addColumn('id_096', array(
			'header'	=> $helper->__('ID_096'),
		));

		// Set Order Number Column
		$this->addColumn('order_id', array(
			'header'	=> $helper->__('Order Number'),
			'align'		=>'right',
			'index'		=> 'order_id',
		));

		// Set ID_098 Column
		$this->addColumn('id_098', array(
			'header'	=> $helper->__('ID_098'),
		));

		// Set ID_099 Column
		$this->addColumn('id_099', array(
			'header'	=> $helper->__('ID_099'),
		));

		// Set ID_100 Column
		$this->addColumn('id_100', array(
			'header'	=> $helper->__('ID_100'),
		));

		// Set ID_101 Column
		$this->addColumn('id_101', array(
			'header'	=> $helper->__('ID_101'),
		));

		// Set ID_102 Column
		$this->addColumn('id_102', array(
			'header'	=> $helper->__('ID_102'),
		));

		// Set ID_103 Column
		$this->addColumn('id_103', array(
			'header'	=> $helper->__('ID_103'),
		));

		// Set ID_104 Column
		$this->addColumn('id_104', array(
			'header'	=> $helper->__('ID_104'),
		));

		// Set ID_105 Column
		$this->addColumn('id_105', array(
			'header'	=> $helper->__('ID_105'),
		));

		// Set ID_106 Column
		$this->addColumn('id_106', array(
			'header'	=> $helper->__('ID_106'),
		));

		// Set ID_107 Column
		$this->addColumn('id_107', array(
			'header'	=> $helper->__('ID_107'),
		));

		// Set ID_108 Column
		$this->addColumn('id_108', array(
			'header'	=> $helper->__('ID_108'),
		));

		// Set ID_109 Column
		$this->addColumn('id_109', array(
			'header'	=> $helper->__('ID_109'),
		));

		// Set ID_110 Column
		$this->addColumn('id_110', array(
			'header'	=> $helper->__('ID_110'),
		));

		// Set ID_111 Column
		$this->addColumn('id_111', array(
			'header'	=> $helper->__('ID_111'),
		));

		// Set ID_112 Column
		$this->addColumn('id_112', array(
			'header'	=> $helper->__('ID_112'),
		));

		// Set Delivery Comment Column
		$this->addColumn('delivery_comment', array(
			'header'	=> $helper->__('Free Field 1'),
			'align'		=> 'left',
			'index'		=> 'delivery_comment',
		));

		// Set Free Field Column
		$this->addColumn('free_field2', array(
			'header'	=> $helper->__('Free Field 2'),
			'align'		=> 'left',
			'index'		=> 'free_field2',
		));

		// Set Free Field Column
		$this->addColumn('free_field3', array(
			'header'	=> $helper->__('Free Field 3'),
			'align'		=> 'left',
			'index'		=> 'free_field3',
		));

		// Set Free Field Column
		$this->addColumn('free_field4', array(
			'header'	=> $helper->__('Free Field 4'),
			'align'		=> 'left',
			'index'		=> 'free_field4',
		));

		// Set Free Field Column
		$this->addColumn('free_field5', array(
			'header'	=> $helper->__('Free Field 5'),
			'align'		=> 'left',
			'index'		=> 'free_field5',
		));

		// Set Order Number Column
		$this->addColumn('shipping_date', array(
			'header'	=> $helper->__('Estimate Shipping Date'),
			'align'		=>'right',
			'index'		=> 'shipping_date',
		));

		// Set ID_119 Column
		$this->addColumn('id_119', array(
			'header'	=> $helper->__('ID_119'),
		));

		// Set ID_120 Column
		$this->addColumn('id_120', array(
			'header'	=> $helper->__('ID_120'),
		));

		// Set ID_121 Column
		$this->addColumn('id_121', array(
			'header'	=> $helper->__('ID_121'),
		));

		// Set ID_122 Column
		$this->addColumn('id_122', array(
			'header'	=> $helper->__('ID_122'),
		));

		// Set ID_123 Column
		$this->addColumn('id_123', array(
			'header'	=> $helper->__('ID_123'),
		));

		// Set ID_124 Column
		$this->addColumn('id_124', array(
			'header'	=> $helper->__('ID_124'),
		));

		// Set ID_125 Column
		$this->addColumn('id_125', array(
			'header'	=> $helper->__('ID_125'),
		));

		// Set ID_126 Column
		$this->addColumn('id_126', array(
			'header'	=> $helper->__('ID_126'),
		));

		// Set Tracking Number Column
		$this->addColumn('tracking_number', array(
			'header'	=> $helper->__('Tracking Number'),
			'align'		=>'right',
			'index'		=> 'tracking_number',
		));

		// Set ID_128 Column
		$this->addColumn('id_128', array(
			'header'	=> $helper->__('ID_128'),
		));

		// Set ID_129 Column
		$this->addColumn('id_129', array(
			'header'	=> $helper->__('ID_129'),
		));

		// Set ID_130 Column
		$this->addColumn('id_130', array(
			'header'	=> $helper->__('ID_130'),
		));

		// Set Don't Put Weight Column
		$this->addColumn('weight_status', array(
			'header'	=> $helper->__("Can't Put Weight"),
			'align'		=> 'center',
			'index'		=> 'weight_status',
		));

		// Set Sort Code Column
		$this->addColumn('sort_code', array(
			'header'	=> $helper->__('Sort Code'),
			'align'		=>'right',
			'index'		=> 'sort_code',
		));

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