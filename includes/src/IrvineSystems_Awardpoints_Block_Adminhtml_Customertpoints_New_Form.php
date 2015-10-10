<?php
/*
 * Irvine Systems Award Points
 *
 * NOTICE OF LICENSE
 * This source file is subject to the Revised BSD License which is bundled with
 * this package in the files LICENSE.txt and LICENSE.html.
 *
 * @category	Magento Sale Extension
 * @package		IrvineSystems_AwardPoints
 * @author Irvine Systems, Inc.
 * @copyright Copyright (c) 2015, Irvine Systems, Inc. (http://irvinesystems.co.jp/)
 * @license     Revised BSD License
 */

class IrvineSystems_Awardpoints_Block_Adminhtml_Customertpoints_New_Form extends Mage_Adminhtml_Block_Widget_Form
{
   /**
    * Prepare Form
    *
    * @return Mage_Adminhtml_Block_Widget_Form
    */
    protected function _prepareForm()
    {
        // Set Data Registry
        $registry = Mage::registry('stats_data');
        // Set Points Model
        $model = Mage::getModel('awardpoints/awardpoints');

        // Initialize Form
		$form = new Varien_Data_Form(array(
            'id'	=> 'edit_form',
            'action'=> $this->getData('action'),
            'method'=> 'post'
        ));

		// Customer Id param, if this is available then we are coming from Statistic for Customer Specific operation
		$cId = $this->getRequest()->getParam('cId');
		
		// Initialize Points Fieldsets
        $fieldset = $form->addFieldset('points_information', array('legend'=>Mage::helper('awardpoints')->__('Customer Points Information')));
        
		// Check if we are working on the points of a specific customer or in general
        // if we are on a specific customer then we do disable the customer ID from Editing
		// Otherwise we set it as required
        if ($cId){
			$fieldset->addField('customer_id', 'select', array(
	            'label'				=> Mage::helper('awardpoints')->__('Customer Name'),
	            'name'				=> 'customer_id',
	            'values'			=> $this->getCustomers($cId),
	            'disabled'			=> true,
	        ));
		}else{
	        $fieldset->addField('customer_id', 'select', array(
	            'label'				=> Mage::helper('awardpoints')->__('Customer Id'),
	            'name'				=> 'customer_id',
	            'values'			=> $this->getCustomers(),
				'after_element_html'=> '<br /><small>'.Mage::helper('awardpoints')->__('Select the customer Id').'</small>',
	            'required'			=> true,
	        ));
		}

        // Check if we need a multi or single selector for the Store ids
        if (!Mage::app()->isSingleStoreMode()) {
            $fieldset->addField('store_id', 'multiselect', array(
                'name'				=> 'store_id[]',
                'label'				=> Mage::helper('awardpoints')->__('Store'),
                'title'				=> Mage::helper('awardpoints')->__('Store'),
                'required'			=> true,
                'values'			=> Mage::getSingleton('adminhtml/system_config_source_store')->toOptionArray(),
				'after_element_html'=> '<br /><small>'.Mage::helper('awardpoints')->__('Select the Store/s in which the point will be available').'</small>',
            ));
        }
        else {
            $fieldset->addField('store_id', 'hidden', array(
                'name'	=> 'store_id[]',
                'value'	=> Mage::app()->getStore(true)->getStoreId()
            ));
            $registry->setStoreId(Mage::app()->getStore(true)->getWebsiteId());
        }

        // Add Action Type Field
        $fieldset->addField('action_type', 'select', array(
            'label'				=> Mage::helper('awardpoints')->__('Type of Action'),
            'name'				=> 'action_type',
            'values'			=> $model->actionsToOptionArray(),
			'after_element_html'=> '<br /><small>'.Mage::helper('awardpoints')->__('Select the action to perform').'</small>',
            'onchange'			=> 'checkAction()',
            'required'			=> true,
        ));

        // Add Point to Add Field
        $fieldset->addField('points_current', 'text', array(
            'label'				=> Mage::helper('awardpoints')->__('Number of Points'),
            'required'			=> true,
			'after_element_html'=> '<br /><small>'.Mage::helper('awardpoints')->__('Insert the amount of points to be added in the customer account').'</small>',
            'class'				=> 'validate-not-negative-number',
            'name'				=> 'points_current',
        ));

        // Add Point to Remove Field
        $fieldset->addField('points_spent', 'text', array(
            'label'				=> Mage::helper('awardpoints')->__('Number of Points'),
            'required'			=> true,
			'after_element_html'=> '<br /><small>'.Mage::helper('awardpoints')->__('Insert the amount of points to be removed from the customer account').'</small>',
            'class'				=> 'validate-not-negative-number',
            'name'				=> 'points_spent',
        ));

        // Add Points Type selector
        $fieldset->addField('points_type', 'select', array(
            'label'				=> Mage::helper('awardpoints')->__('Points Type'),
            'name'				=> 'points_type',
			'after_element_html'=> '<br /><small>'.Mage::helper('awardpoints')->__('Select the type of points').'</small>',
            'values'			=> $model->typesToOptionArray(),
            'onchange'			=> 'checkTarget()',
            'required'			=> true,
        ));

		// Check if we are working on the points of a specific customer or in general
        // if we are on a specific customer then we will show a select controller with all orders for that customer
		// Otherwise a input text box
        if ($cId){
			$fieldset->addField('order_id', 'select', array(
				'label'				=> Mage::helper('awardpoints')->__('Order ID'),
				'name'				=> 'order_id',
				'values'			=> $this->getOrders($cId),
	            'class'				=> 'validate-not-negative-number',
	            'required'			=> true,
				'after_element_html'=> '<br /><small>'.Mage::helper('awardpoints')->__('Select the Order id in which the points will be related').'</small>',
			));
		}else{
	        $fieldset->addField('order_id', 'text', array(
	            'label'				=> Mage::helper('awardpoints')->__('Order ID'),
	            'name'				=> 'order_id',
	            'class'				=> 'validate-not-negative-number',
	            'required'			=> true,
				'after_element_html'=> '<br /><small>'.Mage::helper('awardpoints')->__('Add the Order id in which the points will be related').'</small>',
	        ));
		}

        // Add From Date Field
        $fieldset->addField('date_start', 'date', array(
            'name'				=> 'date_start',
            'title'				=> Mage::helper('awardpoints')->__('From Date'),
            'label'				=> Mage::helper('awardpoints')->__('From Date'),
            'image'				=> Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_SKIN).'/adminhtml/default/default/images/grid-cal.gif',
            'format'			=> Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT),
            'required'			=> true,
			'after_element_html'=> '<br /><small>'.Mage::helper('awardpoints')->__('Select the date from when the points will be available').'</small>',
        ));

        // Add To Date Field
        $fieldset->addField('date_end', 'date', array(
            'name'				=> 'date_end',
            'title'				=> Mage::helper('awardpoints')->__('To Date'),
            'label'				=> Mage::helper('awardpoints')->__('To Date'),
            'image'				=> Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_SKIN).'/adminhtml/default/default/images/grid-cal.gif',
            'format'			=> Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT),
            'required'			=> false,
			'after_element_html'=> '<br /><small>'.Mage::helper('awardpoints')->__('Select the date in which the points will expire. Leave Empty for no expiration time').'</small>',
        ));

		// Finalize the Form
        $form->setUseContainer(true);
        $this->setForm($form);
        return parent::_prepareForm();
    }

   /**
    * Get All Customer on the Store
    * return Structure
    *
    * ['key'] = Customer Id
    * ['value'] = customer Firstname and Lastname
    *
    * @param $id int Unique Customer Id
    * @return array
    */
	private function getCustomers($id=null)
	{
		// get data Collection
		$collection = Mage::getModel('customer/customer')
	                  ->getCollection()
	                  ->addAttributeToSelect('*');

		// pass all customer and add the required custumers to the array
		$result = array();
	    foreach ($collection as $customer) {
        	$cusId = $customer['entity_id'];
			$value = ''.$customer['firstname'].' ' .$customer['lastname'];
			if ($cusId == $id || !$id){
				$result[$cusId] = $value;
			}
	    }
		// return the Array
	    return $result;
	}

   /**
    * Get All Orders for the selected Customer
    * return Structure
    *
    * ['key'] = index
    * ['value'] = Order Number
    *
    * @param $id int Unique Customer Id
    * @return array
    */
	private function getOrders($id=null)
	{
		// get data Collection
		$collection = Mage::getModel('sales/order')
	                  ->getCollection()
	                  ->addAttributeToSelect('*');

		// pass all orders and add the required orders to the array
		$result = array();
		foreach ($collection as $order) {
       		$orderData = $order->getData();
			$ordId = $orderData['customer_id'];
			$value = $orderData['increment_id'];
			if ($ordId == $id || !$id){
				$result[] = $value;
			}
	    }
		// return the Array
		return $result;
	}
}