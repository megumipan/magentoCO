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

class IrvineSystems_Awardpoints_Block_Adminhtml_Customertpoints_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
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

        // Initialize Points Fieldsets
        $fieldset = $form->addFieldset('points_information', array('legend'=>Mage::helper('awardpoints')->__('Customer Points Information')));
        
        // Add Customer Field Field
		$fieldset->addField('customer_id', 'select', array(
            'label'				=> Mage::helper('awardpoints')->__('Customer Name'),
            'name'				=> 'customer_id',
            'values'			=> $this->getCustomer($registry->getCustomerId()),
            'disabled'			=> true,
        ));

        // Check if we need a multi or single selector for the Store ids
        if (!Mage::app()->isSingleStoreMode()) {
            $fieldset->addField('store_id', 'multiselect', array(
                'name'				=> 'store_id[]',
                'label'				=> Mage::helper('awardpoints')->__('Store'),
                'required'			=> true,
                'values'			=> Mage::getSingleton('adminhtml/system_config_source_store')->toOptionArray(),
				'after_element_html'=> '<br /><small>'.Mage::helper('awardpoints')->__('Select the Store/s in which the point will be available').'</small>',
            ));
        }else{
            $fieldset->addField('store_id', 'hidden', array(
                'name'	=> 'store_id[]',
                'value'	=> Mage::app()->getStore(true)->getStoreId()
            ));
            $registry->setStoreId(Mage::app()->getStore(true)->getWebsiteId());
        }

		// Get The Action Type Options
		$pAct=$model->actionsToOptionArray();
		// Check if we the listing is a add point or remove point
        if ($registry->getData('points_current') > 0){
			// Set the Point Type to Current (Add)
			$pType='points_current';
			// Remove 'Remove Points' From the Action Type Option List
			unset($pAct[1]);
		}else{
			// Set the Point Type to Spent (Remove)
			$pType='points_spent';
			// Remove 'Add Points' From the Action Type Option List
			unset($pAct[0]);
		}

        // Add Action Type Field
        $fieldset->addField('action_type', 'select', array(
            'label'				=> Mage::helper('awardpoints')->__('Type of Action'),
            'name'				=> 'action_type',
            'values'			=> $pAct,
            'disabled'			=> true,
        ));

		// Add Points Value Field
        $fieldset->addField($pType, 'text', array(
            'label'				=> Mage::helper('awardpoints')->__('Number of Points'),
            'required'			=> true,
			'after_element_html'=> '<br /><small>'.Mage::helper('awardpoints')->__('Modify the amount of points').'</small>',
            'class'				=> 'validate-not-negative-number',
            'name'				=> $pType,
        ));
        
		// Add Points Type selector
        $fieldset->addField('points_type', 'select', array(
            'label'				=> Mage::helper('awardpoints')->__('Points Type'),
            'name'				=> 'points_type',
            'values'			=> $model->typesToOptionArray(),
            'onchange'			=> 'checkTarget()',
            'disabled'			=> true,
        ));

		// If the listing is about an order show the Order Id
        if ($registry->getOrderId()){
	        $fieldset->addField('order_id', 'text', array(
	            'label'				=> Mage::helper('awardpoints')->__('Order ID'),
	            'name'				=> 'order_id',
	            'class'				=> 'validate-not-negative-number',
	            'disabled'			=> true,
	        ));
		}

        // Add From Date Field
        $fieldset->addField('date_start', 'date', array(
            'name'				=> 'date_start',
            'label'				=> Mage::helper('awardpoints')->__('From Date'),
            'format'			=> Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT),
            'disabled'			=> true,
        ));

        // Add To Date Field
        $fieldset->addField('date_end', 'date', array(
            'name'				=> 'date_end',
            'label'				=> Mage::helper('awardpoints')->__('To Date'),
            'image'				=> Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_SKIN).'/adminhtml/default/default/images/grid-cal.gif',
            'format'			=> Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT),
            'required'			=> false,
			'after_element_html'=> '<br /><small>'.Mage::helper('awardpoints')->__('Select the date in which the points will expire. Leave Empty for no expiration time').'</small>',
        ));

		// Update the Form Data with the Registry Data
		$form->setValues($registry->getData());
	
		// Finalize the Form
        $form->setUseContainer(true);
        $this->setForm($form);
        return parent::_prepareForm();
    }

   /**
    * Get The Customer Full Name
    * return Structure
    *
    * ['key']	= Customer Id
    * ['value'] = customer Firstname and Lastname
    *
    * @param $id int Unique Customer Id
    * @return array
    */
	private function getCustomer($id=null)
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
			    return $result;
			}
	    }
	}
}