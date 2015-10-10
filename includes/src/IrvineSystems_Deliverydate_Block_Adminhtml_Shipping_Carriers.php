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

class IrvineSystems_Deliverydate_Block_Adminhtml_Shipping_Carriers
    extends Mage_Adminhtml_Block_System_Config_Form_Field
{
	// Array Container for add and remove buttons
	protected $_addRowButtonHtml = array();
    protected $_removeRowButtonHtml = array();

    /**
     * Costruct and return Element HTML
     * 
     * @param Varien_Data_Form_Element_Abstract $element HTML element
     * 
     * @return string HTML element Script
     */
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
		// Get current Element
        $this->setElement($element);

		// Construct Template
        $html = '<div id="carriers_methods_template" style="display:none">';
        $html .= $this->_getRowTemplateHtml();
        $html .= '</div>';

		// Construct Container
        $html .= '<ul id="carriers_methods_container">';
        if ($this->_getValue('method')) {
            foreach ($this->_getValue('method') as $i => $f) {
                if ($i) {
                    $html .= $this->_getRowTemplateHtml($i);
                }
            }
        }
        $html .= '</ul>';

		// Add "Add Row Button"
        $html .= $this->_getAddRowButtonHtml('carriers_methods_container',
            'carriers_methods_template', Mage::helper('deliverydate')->__('Add Shipping Method'));

		// Return HTML code
        return $html;
    }

    /**
     * Costruct and return Element HTML
     * 
     * @param int $i roe Id
     * 
     * @return string HTML element Script
     */
    protected function _getRowTemplateHtml($i=0)
    {
		// Open new List Element
        $html = '<li>';

		// Add new selector
        $html .= '<label>' . Mage::helper('deliverydate')->__('Shipping method:') . '</label> ';
        $html .= '<select name="' . $this->getElement()->getName() . '[method][]" ' . $this->_getDisabled() . '>';
        $html .= '<option value="">' . Mage::helper('deliverydate')->__('* Select shipping method') . '</option>';

		// Gather all available Shipping Methods and add it to the Selector Options
        foreach ($this->getShippingMethods() as $carrierCode=>$carrier) {
            $html .= '<optgroup label="' . $carrier['title']
                . '" style="border-top:solid 1px black; margin-top:3px;">';

            foreach ($carrier['methods'] as $methodCode=>$method) {
                //$code = $carrierCode . '/' . $methodCode;
                $code = $method['code'];
                $html .= '<option value="' . $code . '" '
                    . $this->_getSelected('method/' . $i, $code)
                    . ' style="background:white;">' . $method['title'] . '</option>';
            }
            $html .= '</optgroup>';
        }
        $html .= '</select>';

		// Create "Enable/Disable Method" Div
        $html .= '<div style="margin:5px 0 10px;">';
        $html .= '<label>' . Mage::helper('deliverydate')->__('Enable delivery date for this method:') . '</label> ';
        $html .= '<select name="'. $this->getElement()->getName() . '[active][]">';
        $html .= '<option value= 1 ' . $this->_getSelected('active/' . $i, 1) . ' style="background:white;">' . Mage::helper('deliverydate')->__('Enabled') . '</option>';
        $html .= '<option value= 0 ' . $this->_getSelected('active/' . $i, 0) . ' style="background:white;">' . Mage::helper('deliverydate')->__('Disabled') . '</option>';
        $html .= '</select>';
        $html .= '</div>';
		
		// Create "First Day in Stock Item" Div
        $html .= '<div style="margin:5px 0 10px;">';
        $html .= '<label>' . Mage::helper('deliverydate')->__('First day for in stock items:') . '</label> ';
        $html .= '<input class="input-text" style="width:100%;" name="'
            . $this->getElement()->getName() . '[firstStock][]" value="'
            . $this->_getValue('firstStock/' . $i) . '" ' . $this->_getDisabled() . '/> ';
        $html .= '</div>';

		// Create "First Day non in Stock Item" Div
        $html .= '<div style="margin:5px 0 10px;">';
        $html .= '<label>' . Mage::helper('deliverydate')->__('First day for non in stock items:') . '</label> ';
        $html .= '<input class="input-text" style="width:100%;" name="'
            . $this->getElement()->getName() . '[firstNoStock][]" value="'
            . $this->_getValue('firstNoStock/' . $i) . '" ' . $this->_getDisabled() . '/> ';
        $html .= '</div>';

		// Create "Maximum selection Range" Div
        $html .= '<div style="margin:5px 0 10px;">';
        $html .= '<label>' . Mage::helper('deliverydate')->__('Maximum days for selection range:') . '</label> ';
        $html .= '<input class="input-text" style="width:100%;" name="'
            . $this->getElement()->getName() . '[maxRange][]" value="'
            . $this->_getValue('maxRange/' . $i) . '" ' . $this->_getDisabled() . '/> ';
        $html .= '</div>';

		// Create "Shipping Times" Div
        $html .= '<div style="margin:5px 0 10px;">';
        $html .= '<label>' . Mage::helper('deliverydate')->__('Delivery hours:') . '</label> ';
        $html .= '<input class="input-text" style="width:100%;" name="'
            . $this->getElement()->getName() . '[hours][]" value="'
            . $this->_getValue('hours/' . $i) . '" ' . $this->_getDisabled() . '/> ';
        $html .= '</div>';

		// Add "remove Row Button"
        $html .= '<div style="margin:5px 0 10px;">';
        $html .= $this->_getRemoveRowButtonHtml();
        $html .= '</div>';

		// Close List Element
        $html .= '</li>';

		// Return HTML code
        return $html;
    }

    /**
     * Process each store shipping carriers for retrieve their available methods
     * 
     * @return array|string the return vary acording to the carrier (array of methods or string of method)
     */
    protected function getShippingMethods()
    {
        if (!$this->hasData('shipping_methods')) {
            $website = $this->getRequest()->getParam('website');
            $store   = $this->getRequest()->getParam('store');

            $storeId = null;
            if (!is_null($website)) {
                $storeId = Mage::getModel('core/website')
                    ->load($website, 'code')
                    ->getDefaultGroup()
                    ->getDefaultStoreId();
            } elseif (!is_null($store)) {
                $storeId = Mage::getModel('core/store')
                    ->load($store, 'code')
                    ->getId();
            }

            $methods = array();
            $carriers = Mage::getSingleton('shipping/config')->getActiveCarriers($storeId);
            foreach ($carriers as $carrierCode=>$carrierModel) {
                if (!$carrierModel->isActive()) {
                    continue;
                }
                $carrierMethods = $carrierModel->getAllowedMethods();
                if (!$carrierMethods) {
                    continue;
                }
				$carrierTitle = Mage::getStoreConfig('carriers/' . $carrierCode . '/title', $storeId);
                $methods[$carrierCode] = array(
                    'title'   => ''.Mage::helper('deliverydate')->__('Carrier').': ' .$carrierTitle,
                    'methods' => array(),
                );
				foreach ($carrierMethods as $methodCode=>$methodTitle) {
                    $methods[$carrierCode]['methods'][$methodCode] = array(
	                    'title'   => ''.Mage::helper('deliverydate')->__('Method').': ' .$methodTitle,
                        'code' => '' . $carrierCode . '_' . $methodCode,
                    );
                }
            }
            $this->setData('shipping_methods', $methods);
        }
        return $this->getData('shipping_methods');
    }

    /**
     * Getter for Disable flag
     * 
     * @return string HTML element result
     * 
     */
    protected function _getDisabled()
    {
        return $this->getElement()->getDisabled() ? ' disabled' : '';
    }

    /**
     * Getter for value
     * 
     * @param int $key unique id for the requested data
     * 
     * @return string requested data value
     */
    protected function _getValue($key)
    {
        return $this->getElement()->getData('value/' . $key);
    }

    /**
     * Getter for Selected flag
     * 
     * @param int $key unique data id
     * @param int $value data value
     * 
     * @return string HTML element result
     */
    protected function _getSelected($key, $value)
    {
        return $this->getElement()->getData('value/' . $key) == $value ? 'selected="selected"' : '';
    }

    /**
     * Add button "Add additional Row" to Template
     * 
     * @param string $container HTML id element for the target Container
     * @param string $template HTML id element for the target Template
     * @param string $title Button Title (Optional, Default = 'Add')
     * 
     * @return string HTML button Script
     */
	protected function _getAddRowButtonHtml($container, $template, $title='Add')
    {
		// Check if the button is already available in array
        if (!isset($this->_addRowButtonHtml[$container])) {
			// Create new Add button
            $this->_addRowButtonHtml[$container] = $this->getLayout()->createBlock('adminhtml/widget_button')
                    ->setType('button')
                    ->setClass('add ' . $this->_getDisabled())
                    ->setLabel(Mage::helper('deliverydate')->__($title))
                    ->setOnClick("Element.insert($('" . $container . "'), {bottom: $('" . $template . "').innerHTML})")
                    ->setDisabled($this->_getDisabled())
                    ->toHtml();
        }
		// Return HTML code
        return $this->_addRowButtonHtml[$container];
    }

    /**
     * Add button "Remove Row" to Template
     * 
     * @param string $selector HTML identifier for row selector type (Optional, Default = 'li')
     * @param string $title Button Title (Optional, Default = 'Remove')
     * 
     * @return string HTML button Script
     */
    protected function _getRemoveRowButtonHtml($selector = 'li', $title = 'Remove')
    {
		// Check if the HTML code is already available
        if (!$this->_removeRowButtonHtml) {
			// Create new Remove button
            $this->_removeRowButtonHtml = $this->getLayout()->createBlock('adminhtml/widget_button')
                    ->setType('button')
                    ->setClass('delete v-middle ' . $this->_getDisabled())
                    ->setLabel(Mage::helper('deliverydate')->__($title))
                    ->setOnClick("Element.remove($(this).up('" . $selector . "'))")
                    ->setDisabled($this->_getDisabled())
                    ->toHtml();
        }
		// Return HTML code
        return $this->_removeRowButtonHtml;
    }
}