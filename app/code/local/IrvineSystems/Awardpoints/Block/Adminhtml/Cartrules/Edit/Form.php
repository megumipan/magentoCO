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

class IrvineSystems_Awardpoints_Block_Adminhtml_Cartrules_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
   /**
    * Block constructor, prepare Form
    *
    */
    public function __construct()
    {
        // Construct Parent Grid
        parent::__construct();

        // Construct Grid Proprieties
        $this->setId('rule_id');
        $this->setTitle(Mage::helper('awardpoints')->__('Rule Information'));
    }

   /**
    * Prepare Form
    *
    * @return Mage_Adminhtml_Block_Widget_Form
    */
    protected function _prepareForm()
    {
        // Initialize Form
		$form = new Varien_Data_Form(array(
            'id'	=> 'edit_form',
            'action'=> $this->getData('action'),
            'method'=> 'post'
        ));

		// Finalize the Form
        $form->setUseContainer(true);
        $this->setForm($form);
        return parent::_prepareForm();
    }
}