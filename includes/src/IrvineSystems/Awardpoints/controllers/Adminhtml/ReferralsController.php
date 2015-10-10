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

class IrvineSystems_Awardpoints_Adminhtml_ReferralsController extends Mage_Adminhtml_Controller_Action
{
    /**
     * Controller Initialization
     * 
     * @return IrvineSystems_Awardpoints_Adminhtml_StatsController
     */
	protected function _initAction() {
		//load the Layout
		$this->loadLayout()
			->_setActiveMenu('awardpoints/referrals')
			->_addBreadcrumb(Mage::helper('awardpoints')->__('Referrals'), Mage::helper('awardpoints')->__('Referrals'));

		return $this;
	}

    /**
     * Statistics area entry point
     * Render Layout
     * 
     */
	public function indexAction() {
		// Initialize Action Sets
		$this->_initAction()
			->_addContent($this->getLayout()->createBlock('awardpoints/adminhtml_referrals'))
			->renderLayout();
	}

    /**
     * MassDelete Event Handler
     * The function will handle the MassDelete event
     * 
     */
	public function massDeleteAction() {
		// Get all selected Id to be deleted
        $ruleIds = $this->getRequest()->getParam('referral_ids');

		// Get Points Model
		$model = Mage::getModel('awardpoints/referral');

		// This may never happen, but it is better to prevent it
		if(!is_array($ruleIds)) {
				Mage::getSingleton('adminhtml/session')->addError(Mage::helper('awardpoints')->__('No referrals were selected. Please select the referrals to be deleted.'));
				$this->_redirect('*/*');
				return;
		}

		// Delete each Listing in the selection
		foreach ($ruleIds as $ruleId) {
			$rule = $model->load($ruleId);
			$rule->delete();
		}
             
		// Inform the admin Panel for the success
		Mage::getSingleton('adminhtml/session')->addSuccess(
			Mage::helper('awardpoints')->__(
				'A Total of %d referrals data were successfully deleted', count($ruleIds)
			)
		);

		// Redirect to refresh page
		$this->_redirect('*/*/');
    }
}