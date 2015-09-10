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

class IrvineSystems_Awardpoints_Block_Adminhtml_Catalogrules extends Mage_Adminhtml_Block_Widget_Grid_Container
{
   /**
    * Block constructor, prepare grid params
    *
    */
    public function __construct()
    {
		// Set Block Controller
        $this->_controller = 'adminhtml_catalogrules';
		// Set BlockGroup
        $this->_blockGroup = 'awardpoints';
		// Set Header Title
        $this->_headerText = Mage::helper('awardpoints')->__('Catalog Points Rules');
		// Change the Label for the "add" button
		$this->_addButtonLabel = Mage::helper('awardpoints')->__('Add New Catalog Rule');
		// Construct Parent Container
        parent::__construct();
    }
}