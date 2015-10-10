<?php

class Rack_Point_Block_Adminhtml_Customer_Form extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * Get current customer
     *
     * @return Mage_Customer_Model_Customer
     */
    public function getCustomer()
    {
        return Mage::registry('current_customer');
    }

    /**
     * Prepare form before rendering HTML
     *
     * @return Rack_Point_Block_Adminhtml_Customer_Form
     */
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $form->setHtmlIdPrefix('rackpoint_');
        $form->setFieldNameSuffix('rackpoint');
        $fieldset = $form->addFieldset('update_fieldset', array(
            'legend' => Mage::helper('rackpoint')->__('Update Points Balance')
        ));

        if (!Mage::app()->isSingleStoreMode()) {
            $fieldset->addField('website', 'select', array(
                'name'  => 'website_id',
                'title' => Mage::helper('rackpoint')->__('Website'),
                'label' => Mage::helper('rackpoint')->__('Website'),
                'values' => $this->_getWebsiteValues()
            ));
        }

        $fieldset->addField('points_delta', 'text', array(
            'name'  => 'points_delta',
            'title' => Mage::helper('rackpoint')->__('Update Points'),
            'label' => Mage::helper('rackpoint')->__('Update Points'),
            'note'  => Mage::helper('rackpoint')->__('Enter a negative number to subtract from balance.')
        ));

        $fieldset->addField('comment', 'textarea', array(
            'name'  => 'comment',
            'title' => Mage::helper('rackpoint')->__('Comment'),
            'label' => Mage::helper('rackpoint')->__('Comment'),
            'style' => 'height:50px; width:500px'
        ));

        $this->setForm($form);
        return parent::_prepareForm();
    }

    /**
     * Retrieve source values for store drop-dawn
     *
     * @return array
     */
    protected function _getStoreValues()
    {
        $customer = $this->getCustomer();
        if (!$customer->getWebsiteId()
            || Mage::app()->isSingleStoreMode()
            || $customer->getSharingConfig()->isGlobalScope())
        {
            return Mage::getModel('adminhtml/system_store')->getStoreValuesForForm();
        }

        $stores = Mage::getModel('adminhtml/system_store')
            ->getStoresStructure(false, array(), array(), array($customer->getWebsiteId()));
        $values = array();

        $nonEscapableNbspChar = html_entity_decode('&#160;', ENT_NOQUOTES, 'UTF-8');
        foreach ($stores as $websiteId => $website) {
            $values[] = array(
                'label' => $website['label'],
                'value' => array()
            );
            if (isset($website['children']) && is_array($website['children'])) {
                foreach ($website['children'] as $groupId => $group) {
                    if (isset($group['children']) && is_array($group['children'])) {
                        $options = array();
                        foreach ($group['children'] as $storeId => $store) {
                            $options[] = array(
                                'label' => str_repeat($nonEscapableNbspChar, 4) . $store['label'],
                                'value' => $store['value']
                            );
                        }
                        $values[] = array(
                            'label' => str_repeat($nonEscapableNbspChar, 4) . $group['label'],
                            'value' => $options
                        );
                    }
                }
            }
        }
        return $values;
    }
    
    protected function _getWebsiteValues()
    {
        return Mage::getResourceSingleton('core/website_collection')->toOptionArray();
    }
}