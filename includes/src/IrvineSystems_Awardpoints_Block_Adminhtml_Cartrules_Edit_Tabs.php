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

class IrvineSystems_Awardpoints_Block_Adminhtml_Cartrules_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
   /**
    * Block constructor, prepare Selectable Tabs
    *
    */
    public function __construct()
    {
        // Construct Parent Tabs
        parent::__construct();

        // Construct Tabs Proprieties
        $this->setId('rule_id');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('awardpoints')->__('Shopping Cart Points Rules'));
    }

   /**
    * Prepare Tabs for HTML
    *
    * @return Mage_Adminhtml_Block_Widget_Tabs
    */
    protected function _beforeToHtml()
    {
		// Add Main Section Tab
        $this->addTab('main_section', array(
            'label'     => Mage::helper('awardpoints')->__('Rule Information'),
            'title'     => Mage::helper('awardpoints')->__('Rule Information'),
            'content'   => $this->getLayout()->createBlock('awardpoints/adminhtml_cartrules_edit_tab_main')->toHtml(),
            'active'    => true
        ));

		// Add Conditions Section Tab
        $this->addTab('conditions_section', array(
            'label'     => Mage::helper('awardpoints')->__('Conditions'),
            'title'     => Mage::helper('awardpoints')->__('Conditions'),
            'content'   => $this->getLayout()->createBlock('awardpoints/adminhtml_cartrules_edit_tab_conditions')->toHtml(),
        ));

		// Add Action Section Tab
        $this->addTab('actions_section', array(
            'label'     => Mage::helper('awardpoints')->__('Actions'),
            'title'     => Mage::helper('awardpoints')->__('Actions'),
            'content'   => $this->getLayout()->createBlock('awardpoints/adminhtml_cartrules_edit_tab_actions')->toHtml(),
        ));

        return parent::_beforeToHtml();
    }
}