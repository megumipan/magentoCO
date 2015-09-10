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

class IrvineSystems_Awardpoints_Block_Adminhtml_Customertpoints extends Mage_Adminhtml_Block_Widget_Grid_Container
{
   /**
    * Block constructor, prepare grid params
    *
    */
	public function __construct()
	{
		$cId = $this->getRequest()->getParam('cId');
		// Set Block Controller
		$this->_controller = 'adminhtml_customertpoints';
		// Set BlockGroup
		$this->_blockGroup = 'awardpoints';
		// Set Header Title
		$this->_headerText = Mage::helper('awardpoints')->__('Customers Points History');
		// Change the Label for the "add" button
		$this->_addButtonLabel = Mage::helper('awardpoints')->__('Add Points Event');
		// Construct Parent Container
		parent::__construct();
        
		$this->_updateButton('add', 'onclick', 'setLocation(\''.$this->getAddUrl().'\')');
		
		// If we are coming from statistics, we will add a button to restore the full clients view
		if ($this->getRequest()->getParam('cId')){
			$this->_addButton ('check_orders_points', array(
				'label'		=> Mage::helper('awardpoints')->__('Show All Customers'),
				'onclick'	=> 'setLocation(\''.$this->getResetUrl().'\')'));
		};
	}

    /**
     * Prepare Check Points URL
     *
     * @return string
     */
	public function getResetUrl()
	{
		return $this->getUrl('*/*/');
	}

    /**
     * Prepare Add URL
     *
     * @return string
     */
    public function getAddUrl()
    {
		// If we have a customer id add it in Post
		if ($cId = $this->getRequest()->getParam('cId')){
			return $this->getUrl('*/*/new', array('cId'=>$cId));
		}
		// If not, no need for post data
		return $this->getUrl('*/*/new');
    }
    
}