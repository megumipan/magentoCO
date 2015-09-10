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

class IrvineSystems_Awardpoints_Block_Adminhtml_Customertpoints_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
   /**
    * Block constructor, prepare form Container params
    *
    */
    public function __construct()
    {
		// Construct Parent Container
        parent::__construct();

        // Set Block Data
        $this->_objectId	= 'id';
        $this->_blockGroup	= 'awardpoints';
        $this->_controller	= 'adminhtml_customertpoints';
		
        // Update save Buttons Label
        $this->_updateButton('save', 'label', Mage::helper('awardpoints')->__('Save Points'));
        $this->_updateButton('delete', 'label', Mage::helper('awardpoints')->__('Delete Points'));
    }

   /**
    * Prepare Block Header
    *
    * @return string
    */
    public function getHeaderText()
    {
        return Mage::helper('awardpoints')->__('Edit Customer Points');
    }

   /**
    * Prepare Block Form HTML
    *
    */
    public function getFormHtml()
    {
        return $this->getLayout()
            ->createBlock('awardpoints/adminhtml_customertpoints_edit_form')
            ->setAction($this->getSaveUrl())
            ->toHtml();
    }

    /**
     * Prepare Back URL
     *
     * @return string
     */
    public function getBackUrl()
    {
		// If we have a customer id add it in Post
		if ($cId = $this->getRequest()->getParam('cId')){
			return $this->getUrl('*/*/', array('cId'=>$cId));
		}
		// If not, no need for post data
		return $this->getUrl('*/*/');
    }
    
    /**
     * Prepare Delete URL
     *
     * @return string
     */
    public function getDeleteUrl()
    {
        return $this->getUrl('*/*/delete', array('_current'=>true));
    }
    
    /**
     * Prepare Save URL
     *
     * @return string
     */
    public function getSaveUrl()
    {
        return $this->getUrl('*/*/save', array('_current'=>true));
    }
    
}