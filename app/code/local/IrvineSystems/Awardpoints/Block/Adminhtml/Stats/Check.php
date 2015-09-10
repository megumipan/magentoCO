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

class IrvineSystems_Awardpoints_Block_Adminhtml_Stats_Check extends Mage_Adminhtml_Block_Widget_Form_Container
{
   /**
    * Block constructor, prepare Form Container params
    *
    */
    public function __construct()
    {
		// Construct Parent Container
		parent::__construct();

        // Set Block Data
		$this->_objectId	= 'id';
        $this->_blockGroup	= 'awardpoints';
        $this->_controller	= 'adminhtml_stats';

        // Update save Button Label
        $this->_updateButton('save', 'label', Mage::helper('awardpoints')->__('Start Checking'));
    }

   /**
    * Prepare Block Header
    *
    * @return string
    */
    public function getHeaderText()
    {
        return Mage::helper('awardpoints')->__('Refresh Orders Points');
    }

   /**
    * Prepare Block Form HTML
    *
    */
    public function getFormHtml()
    {
        return $this->getLayout()
            ->createBlock('awardpoints/adminhtml_stats_edit_form')
            ->setAction($this->getSaveUrl())
            ->toHtml();
    }

    /**
     * Prepare Save URL
     *
     * @return string
     */
    public function getSaveUrl()
    {
        return $this->getUrl('*/*/savecheck', array('_current'=>true));
    }
}