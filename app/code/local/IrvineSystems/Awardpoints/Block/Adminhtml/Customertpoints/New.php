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

class IrvineSystems_Awardpoints_Block_Adminhtml_Customertpoints_New extends Mage_Adminhtml_Block_Widget_Form_Container
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
		
		// Remove delete Button we will not need it on a new listing
		$this->_removeButton('delete');

        // Update save Buttons Label
        $this->_updateButton('save', 'label', Mage::helper('awardpoints')->__('Save Points'));
		
        // Set Types Definition for JavaScript
		$ordType = Mage::getModel('awardpoints/awardpoints')->getOrderPointsType();
		$addType = Mage::getModel('awardpoints/awardpoints')->getAddActionType();
		
        // Javascript to hide and show the Point fields according if they are needed or not
		// TODO: Move Js in a propper place (as design Layout extension)
        $this->_formScripts[] = "
			// Do checking for default values after load
            document.observe('dom:loaded', function() {
                checkTarget();
                checkAction();
            });
			
			// Target selector Function
			function checkTarget(){
				if ($('points_type').getValue() == ".$ordType."){
                    $('order_id').parentNode.parentNode.show();
			        $('order_id').addClassName('required-entry').removeClassName('validate-not-negative-number');
                } else {
                    $('order_id').parentNode.parentNode.hide();
			        $('order_id').removeClassName('required-entry').removeClassName('validate-not-negative-number');
                }                
            };           

			// Action selector Function
            function checkAction(){
                if ($('action_type').getValue() == ".$addType."){
			        $('points_current').addClassName('required-entry validate-not-negative-number');
                    $('points_current').parentNode.parentNode.show();
                    $('points_spent').parentNode.parentNode.hide();
                    $('points_spent').value='';
			        $('points_spent').removeClassName('required-entry').removeClassName('validate-not-negative-number');
                } else {
			        $('points_spent').addClassName('required-entry validate-not-negative-number');
                    $('points_spent').parentNode.parentNode.show();
                    $('points_current').value='';
                    $('points_current').parentNode.parentNode.hide();
			        $('points_current').removeClassName('required-entry').removeClassName('validate-not-negative-number');
                }                
            };           
        ";
    }

   /**
    * Prepare Block Header
    *
    * @return string
    */
    public function getHeaderText()
    {
		return Mage::helper('awardpoints')->__('Add Points Event');
    }

   /**
    * Prepare Block Form HTML
    *
    */
    public function getFormHtml()
    {
        return $this->getLayout()
            ->createBlock('awardpoints/adminhtml_customertpoints_new_form')
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