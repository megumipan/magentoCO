<?php
/*
 * Irvine Systems Shipping Japan Ymt
 *
 * NOTICE OF LICENSE
 * This source file is subject to the Revised BSD License which is bundled with
 * this package in the files LICENSE.txt and LICENSE.html.
 *
 * @category	Magento Shipping Extension
 * @package		IrvineSystems_Yamato
 * @author Irvine Systems, Inc.
 * @copyright Copyright (c) 2015, Irvine Systems, Inc. (http://irvinesystems.co.jp/)
 * @license     Revised BSD License
 */

class IrvineSystems_Yamato_Block_Adminhtml_Import_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    /**
     * Constructor
     *
     * @return void
     */
	public function __construct()
    {
        // Parent Constructor
		parent::__construct();

        // Remove back and reset Button and update save button Label
        $this->removeButton('back')
            ->removeButton('reset')
            ->_updateButton('save', 'label', Mage::helper('yamato')->__('Import Data'));
	}

    /**
     * Internal constructor
     *
     * @return void
     */
    protected function _construct()
    {
        // Parent Constructor
		parent::_construct();

        // Sel Block Parameters
        $this->_objectId   = 'import_id';
        $this->_blockGroup = 'yamato';
        $this->_controller = 'adminhtml_import';
    }

    /**
     * To Html Handler
     *
     * @return void
     */
    protected function _toHtml() {
		// Return parent _toHtml
        return parent::_toHtml();

    }

    /**
     * Get header text
     *
     * @return string
     */
    public function getHeaderText()
    {
        return Mage::helper('yamato')->__('YAMATO - Import Shipment Slips Data');
    }
}