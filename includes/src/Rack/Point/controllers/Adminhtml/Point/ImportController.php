<?php

class Rack_Point_Adminhtml_Point_ImportController extends Mage_Adminhtml_Controller_Action
{

    public function indexAction()
    {
        $this->loadLayout();
        $form = $this->getLayout()->createBlock('rackpoint/adminhtml_import_form', 'import.form');
        $this->_addContent($form);
        $this->_getSession()->setPointDataFile(null);
        
        $this->renderLayout();
    }

    public function previewAction()
    {
        if (!$this->getRequest()->isPost()) {
            $this->_redirect('*/*');
            return;
        }
        try {
            $uploader = new Varien_File_Uploader('data_file');
            $uploader->setAllowedExtensions(array('csv', 'tsv', 'txt'));

            $uploader->setAllowRenameFiles(false);
            $uploader->setFilesDispersion(false);
            $path = Mage::getBaseDir('var') . DS . 'upload' . DS;
            $uploader->save($path, md5($this->_getSession()->getSessionId()) . '_' . $_FILES['data_file']['name']);
            $filename = $path . $uploader->getUploadedFileName();
            $this->_getSession()->setPointDataFile($filename);
            
            $this->loadLayout();
            $this->renderLayout();
        } catch (Exception $e) {
            $this->_getSession()->addError($e->getMessage());
            $this->_redirect('*/*');
        }
    }

    public function gridAction()
    {
        $preview = $this->getLayout()->createBlock('rackpoint/adminhtml_import_preview_grid', 'import.preview.grid');
        $this->getResponse()->setBody($preview->toHtml());
    }

    public function importAction()
    {
        $filename = $this->_getSession()->getPointDataFile();
        $response = array('success' => false, 'message' => '', 'validate_error' => false);
        try {
            if ($websiteId = $this->getRequest()->getPost('website_id')) {
                $result = Mage::getResourceModel('rackpoint/point_balance')->import($filename, $websiteId);
                if ($result !== true) {
                    $response['message'] = $this->__('Error: %s', $result);
                } else {
                    $response['success'] = true;
                    $response['message'] = $this->__('Import point successful!');
                }
            } else {
                $response['validate_error'] = true;
                $response['message'] =  $this->__('Please select website');
            }
        } catch (Exception $e) {
            Mage::logException($e);
            $response['message'] =  $this->__('Error: %s', $e->getMessage());
        }
        if ($response['validate_error'] != true) {
            @unlink($filename);
        }
        echo json_encode($response);
        exit;
    }

    public function statusAction()
    {
        
    }

    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('rackpoint');
    }

}