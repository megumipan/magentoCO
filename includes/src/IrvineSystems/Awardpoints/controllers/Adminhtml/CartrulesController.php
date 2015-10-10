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

class IrvineSystems_Awardpoints_Adminhtml_CartrulesController extends Mage_Adminhtml_Controller_Action
{
    /**
     * Controller Initialization
     * 
     * @return IrvineSystems_Awardpoints_Adminhtml_StatsController
     */
    protected function _initAction() {
		//load the Layout
        $this->loadLayout()
                ->_setActiveMenu('awardpoints/cartrules')
                ->_addBreadcrumb(Mage::helper('awardpoints')->__('Point Rules'), Mage::helper('awardpoints')->__('Point Rules'));

        return $this;
    }

    /**
     * Customers Points area entry point
     * Render Layout
     * 
     */
    public function indexAction() {
		// Initialize Action Sets
        $this->_initAction()
			->_addContent($this->getLayout()->createBlock('awardpoints/adminhtml_cartrules'))
			->renderLayout();
    }

    /**
     * New Listing Event Handler
     * The function will handle the New listing event
     * 
     */
    public function newAction()
    {
        // Forward the Action to edit (share same management)
        $this->_forward('edit');
    }

    /**
     * Edit Listing Event Handler
     * The function will handle the Edit listing event
     * 
     */
    public function editAction()
    {
		// Load model data for the selected listing
        $id = $this->getRequest()->getParam('id');
		$model = Mage::getModel('awardpoints/cartrules');

		// Errror control if a rule get erased after beign selected
        if ($id) {
            $model->load($id);
            if (! $model->getRuleId()) {
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('awardpoints')->__('This rule no longer exists'));
                $this->_redirect('*/*');
                return;
            }
        }

		// Set the Form Data
        $data = Mage::getSingleton('adminhtml/session')->getPageData(true);
		if($data) $model->addData($data);

		// Update Java Widget confitions
        $model->getConditions()->setJsFormObject('cartrules_conditions_fieldset');
        $model->setData('segments_cut', explode(';', $model->getSegmentsCut()));
        $model->setData('segments_paste', explode(';', $model->getSegmentsPaste()));

		// Update Catalog Rules Register
        Mage::register('cartrules_data', $model);

		// Load Layout and set Block Parameters
        $this->loadLayout();
        $this->_setActiveMenu('awardpoints');
        $block = $this->getLayout()->createBlock('awardpoints/adminhtml_cartrules_edit')
            ->setData('action', $this->getUrl('*/awardpoints_cartrules/save'));

		// Enable Javasript for the Page
        $this->getLayout()->getBlock('head')
            ->setCanLoadExtJs(true)
            ->setCanLoadRulesJs(true);

		// Add main and left content to layout
        $this->_addContent($block)
			->_addLeft($this->getLayout()->createBlock('awardpoints/adminhtml_cartrules_edit_tabs'))
            ->renderLayout();
    }

    /**
     * Save Points Event Handler
     * The function will Save the newly added or edited points for the client
     * 
     */
    public function saveAction()
    {
		// Check if we have POST information to process
		// This may never happen, but it is better to prevent it
		if (!$this->getRequest()->getParams()) {
				Mage::getSingleton('adminhtml/session')->addError(Mage::helper('awardpoints')->__('Rule data to be saved were not found. Please refresh the page and try again'));
				$this->_redirect('*/*');
				return;
		}

		// Set the data Array
		$data = $this->getRequest()->getParams();
		// Get Points Model
		$model = Mage::getModel('awardpoints/cartrules');

		//Reference to rule Id
		$ruleId = null;
		if (isset($data['rule_id'])) $ruleId = $data['rule_id'];
		
		//If we are editing a rule, then load the rule data
		if ($ruleId) {
			$model->load($ruleId);
			// Check if by any change the ids are not matching
			if ($ruleId != $model->getId()) {
				Mage::throwException(Mage::helper('awardpoints')->__('Wrong rule specified.'));
                $this->_redirect('*/*/');
				return;
			}
		}

		// Convert the data format
		$data = $this->_filterDates($data, array('from_date', 'to_date'));

		// Check if all rule datas are correct
		$validateResult = $model->validateData(new Varien_Object($data));

		// If the data is invalid parse the error message and return to edit
		if (!$validateResult) {
			foreach($validateResult as $errorMessage) {
				$this->_getSession()->addError($errorMessage);
			}
			$this->_getSession()->setPageData($data);
			$this->_redirect('*/*/edit', array('id'=>$model->getId()));
			return;
		}

		// Shift out the Conditions...
		$data['conditions'] = $data['rule']['conditions'];
		// ... and unset the rule array
		unset($data['rule']);

		// Set the Model Data
		$model->loadPost($data);
		// Set singleton page data
		Mage::getSingleton('adminhtml/session')->setPageData($model->getData());
		// Save the Model
		$model->save();

		// Inform the admin Panel for the success
		Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('awardpoints')->__('Rule was successfully saved'));
		// Reset Singleton Page Data
		Mage::getSingleton('adminhtml/session')->setPageData(false);

		// Return to main Grid
		$this->_redirect('*/*/');
    }

    /**
     * Delete Points Event Handler
     * The function will handle the Delete listing event
     * 
     */
    public function deleteAction()
    {
		// Get request id
		$id = $this->getRequest()->getParam('id');
		// Get Points Model
		$model = Mage::getModel('awardpoints/cartrules');
		// Load the requested id in the Model
		$model->load($id);
		// Delete the requested id
		$model->delete();
		// Inform the admin Panel for the success
		Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('awardpoints')->__('Rule was successfully deleted'));
		// Return to main Grid
		$this->_redirect('*/*/');
    }

    /**
     * New Condition Html Event Handler
     * Update the selected model as Points rule
     * 
     */
    public function newConditionHtmlAction()
    {
        //　Get param data
        $id = $this->getRequest()->getParam('id');
        $typeArr = explode('|', str_replace('-', '/', $this->getRequest()->getParam('type')));
        $type = $typeArr[0];

        // Update the selecte model for Points rule
        $model = Mage::getModel($type)
            ->setId($id)
            ->setType($type)
            ->setRule(Mage::getModel('awardpoints/cartrules'))
            ->setPrefix('conditions');
        if (!empty($typeArr[1])) {
            $model->setAttribute($typeArr[1]);
        }

        // Check if the condition is Valid and update the Html
        if ($model instanceof Mage_Rule_Model_Condition_Abstract) {
            $model->setJsFormObject($this->getRequest()->getParam('form'));
            $html = $model->asHtmlRecursive();
        } else {
            $html = '';
        }

        // Update the page body
        $this->getResponse()->setBody($html);
    }
}