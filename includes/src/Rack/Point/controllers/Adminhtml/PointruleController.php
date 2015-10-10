<?php

class Rack_Point_Adminhtml_PointruleController extends Mage_Adminhtml_Controller_Action
{
    public function indexAction()
    {
        $dirtyRules = Mage::getModel('rackpoint/flag')->loadSelf();
        if ($dirtyRules->getState()) {
            $this->_getSession()->addNotice($this->__('There are rules that have been changed but were not applied. Please, click Apply Rules in order to see immediate effect in the catalog.'));
        }

        if (Mage::helper('rackpoint')->isPerOrderMode()) {
            $this->_getSession()->addNotice($this->__('You are using order mode. Point rules are not used to calculate point if you are using order mode.'));
        }

        $this->loadLayout();

        $this->_title($this->__('Manage Point Rule'))->_title($this->__('Rack Point'));
        $this->_setActiveMenu('rackpoint/pointrule');
        
        $block = $this->getLayout()->createBlock('rackpoint/adminhtml_rule');
        $this->_addContent($block);

        $this->renderLayout();
    }
    
    public function newAction()
    {
        $this->_forward('edit');
    }

    public function editAction()
    {
        $id = $this->getRequest()->getParam('id');
        $model = Mage::getModel('rackpoint/catalog_rule');

        if ($id) {
            $model->load($id);
            if (! $model->getRuleId()) {
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('rackpoint')->__('This rule no longer exists.'));
                $this->_redirect('*/*');
                return;
            }
        }

        $this->_title($model->getRuleId() ? $model->getName() : $this->__('New Point Rule'));

        // set entered data if was error when we do save
        $data = Mage::getSingleton('adminhtml/session')->getPageData(true);
        if (!empty($data)) {
            $model->addData($data);
        }
        $model->getConditions()->setJsFormObject('rule_conditions_fieldset');

        Mage::register('current_point_rule', $model);
        
        $this->loadLayout()->getLayout()->getBlock('point_rule_edit')
             ->setData('form_action_url', $this->getUrl('adminhtml/pointrule/save'));
        $this->_setActiveMenu('rackpoint/pointrule');
        $this
            ->_addBreadcrumb($id ? Mage::helper('rackpoint')->__('Edit Rule') : Mage::helper('rackpoint')->__('New Rule'), $id ? Mage::helper('catalogrule')->__('Edit Rule') : Mage::helper('catalogrule')->__('New Rule'))
            ->renderLayout();

    }

    public function saveAction()
    {
        if ($this->getRequest()->getPost()) {
            
            try {
                $model = Mage::getModel('rackpoint/catalog_rule');
                $data = $this->getRequest()->getPost();
                $data = $this->_filterDates($data, array('from_date', 'to_date'));
                if ($id = $this->getRequest()->getParam('rule_id')) {
                    $model->load($id);
                    if ($id != $model->getId()) {
                        Mage::throwException(Mage::helper('rackpoint')->__('Wrong rule specified.'));
                    }
                }

                $validateResult = $model->validateData(new Varien_Object($data));
                if ($validateResult !== true) {
                    foreach($validateResult as $errorMessage) {
                        $this->_getSession()->addError($errorMessage);
                    }
                    $this->_getSession()->setPageData($data);
                    $this->_redirect('*/*/edit', array('id'=>$model->getId()));
                    return;
                }

                $data['conditions'] = $data['rule']['conditions'];
                unset($data['rule']);

                if (!empty($data['auto_apply'])) {
                    $autoApply = true;
                    unset($data['auto_apply']);
                } else {
                    $autoApply = false;
                }

                $model->loadPost($data);

                Mage::getSingleton('adminhtml/session')->setPageData($model->getData());

                $model->save();

                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('rackpoint')->__('The rule has been saved.'));
                Mage::getSingleton('adminhtml/session')->setPageData(false);
                if ($autoApply) {
                    $this->_forward('applyRules');
                } else {
                    Mage::getModel('rackpoint/flag')->loadSelf()
                        ->setState(1)
                        ->save();
                    $this->_redirect('*/*/');
                }
                return;
            } catch (Mage_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            } catch (Exception $e) {
                $this->_getSession()->addError(Mage::helper('rackpoint')->__('An error occurred while saving the rule data. Please review the log and try again.'));
                Mage::logException($e);
                Mage::getSingleton('adminhtml/session')->setPageData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('rule_id')));
                return;
            }
        }
        $this->_redirect('*/*/');
    }

    public function deleteAction()
    {
        if ($id = $this->getRequest()->getParam('id')) {
            try {
                $model = Mage::getModel('rackpoint/catalog_rule');
                $model->load($id);
                $model->delete();
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('rackpoint')->__('The rule has been deleted.'));
                $this->_redirect('*/*/');
                return;
            } catch (Mage_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            } catch (Exception $e) {
                $this->_getSession()->addError(Mage::helper('rackpoint')->__('An error occurred while deleting the rule. Please review the log and try again.'));
                Mage::logException($e);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('rackpoint')->__('Unable to find a rule to delete.'));
        $this->_redirect('*/*/');
    }

    public function newConditionHtmlAction()
    {
        $id = $this->getRequest()->getParam('id');
        $typeArr = explode('|', str_replace('-', '/', $this->getRequest()->getParam('type')));
        $type = $typeArr[0];

        $model = Mage::getModel($type)
            ->setId($id)
            ->setType($type)
            ->setRule(Mage::getModel('rackpoint/catalog_rule'))
            ->setPrefix('conditions');
        if (!empty($typeArr[1])) {
            $model->setAttribute($typeArr[1]);
        }

        if ($model instanceof Mage_Rule_Model_Condition_Abstract) {
            $model->setJsFormObject($this->getRequest()->getParam('form'));
            $html = $model->asHtmlRecursive();
        } else {
            $html = '';
        }
        $this->getResponse()->setBody($html);
    }

    public function chooserAction()
    {
        switch ($this->getRequest()->getParam('attribute')) {
            case 'sku':
                $type = 'adminhtml/promo_widget_chooser_sku';
                break;

            case 'categories':
                $type = 'adminhtml/promo_widget_chooser_categories';
                break;
        }
        if (!empty($type)) {
            $block = $this->getLayout()->createBlock($type);
            if ($block) {
                $this->getResponse()->setBody($block->toHtml());
            }
        }
    }

    public function newActionHtmlAction()
    {
        $id = $this->getRequest()->getParam('id');
        $typeArr = explode('|', str_replace('-', '/', $this->getRequest()->getParam('type')));
        $type = $typeArr[0];

        $model = Mage::getModel($type)
            ->setId($id)
            ->setType($type)
            ->setRule(Mage::getModel('rackpoint/catalog_rule'))
            ->setPrefix('actions');
        if (!empty($typeArr[1])) {
            $model->setAttribute($typeArr[1]);
        }

        if ($model instanceof Mage_Rule_Model_Action_Abstract) {
            $model->setJsFormObject($this->getRequest()->getParam('form'));
            $html = $model->asHtmlRecursive();
        } else {
            $html = '';
        }
        $this->getResponse()->setBody($html);
    }

    /**
     * Apply all active catalog price rules
     */
    public function applyRulesAction()
    {
        try {
            Mage::getModel('rackpoint/catalog_rule')->applyAll();
            Mage::getModel('rackpoint/flag')->loadSelf()
                ->setState(0)
                ->save();
            Mage::getSingleton('adminhtml/session')->addSuccess(
                Mage::helper('rackpoint')->__('The rules have been applied.')
            );
        } catch (Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError(
                Mage::helper('rackpoint')->__('Unable to apply rules.')
            );
            throw $e;
        }
        $this->_redirect('*/*');
    }
    
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('rackpoint/pointrule');
    }
}