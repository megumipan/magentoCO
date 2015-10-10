<?php
/*
 * Irvine Systems Delivery Date Optimum
 *
 * NOTICE OF LICENSE
 * This source file is subject to the Revised BSD License which is bundled with
 * this package in the files LICENSE.txt and LICENSE.html.
 *
 * @category	Magento Catalog Extension
 * @package		IrvineSystems_Deliverydate
 * @author Irvine Systems, Inc.
 * @copyright Copyright (c) 2015, Irvine Systems, Inc. (http://irvinesystems.co.jp/)
 * @license     Revised BSD License
 */
 
class IrvineSystems_Deliverydate_Block_Customer_Info extends Mage_Core_Block_Template
{
    /**
     * Block Construction
     * 
     */
	public function __construct()
	{
		// Set the Block Template
		$this->setTemplate('deliverydate/customer/info.phtml');
	}
 
    /**
     * Check if the Delivery Date can be display
     * 
     * @return bool
     */
    public function displayDeliveryDate()
    {
		// Get order instance from the registry
		$order = $this->getOrder();

 		// Check if a date, time or comment is available
        if($order->getShippingDeliveryDate() != NULL || $order->getShippingDeliveryTime() != NULL || $order->getShippingDeliveryComments() != NULL)
            // If it is enable the render
			return true;

 		// If data are not available return false
        return false;
    }
 
    /**
     * Get the delivery date information
     * 
     * @return string Html output for layout
     */
    public function getDeliveryDateInfo()
    {
		// Get order instance from the registry
		$order = $this->getOrder();
		// Get Config and Set Default Values
		$date_format		= Mage::getStoreConfig('deliverydate/general/date_format');
		$deliveryDate		= Mage::helper('deliverydate')->__('N/A');
		$deliveryTime		= Mage::helper('deliverydate')->__('N/A');
		$deliveryComments	= Mage::helper('deliverydate')->__('N/A');
						
		// If values are available det the Update the Default Values
		if ($date_format=='') $date_format='d/M/Y';
		if ($order->getShippingDeliveryDate()!='') $deliveryDate = date ($date_format,strtotime($order->getShippingDeliveryDate()));
		if ($order->getShippingDeliveryTime()!='') $deliveryTime = $order->getShippingDeliveryTime();
		if ($order->getShippingDeliveryComments()!='') $deliveryComments = $order->getShippingDeliveryComments();

		// Format the HTML output
		$html = '';
		$html .= Mage::helper('deliverydate')->__('<b>Date:</b> %s', $deliveryDate);
		$html .= '<br/>';
		$html .= Mage::helper('deliverydate')->__('<b>Time:</b> %s', $deliveryTime);
		$html .= '<br/>';
		$html .= Mage::helper('deliverydate')->__('<b>Comments:</b> %s', $deliveryComments);
					
		// Return HHTML string
		return $html;
    }
 
    /**
     * Get the current order information from the registry
     * 
     * @return MAge_Sales_Model_Order
     */
    protected function getOrder()
    {
        return Mage::registry('current_order');
    }
}