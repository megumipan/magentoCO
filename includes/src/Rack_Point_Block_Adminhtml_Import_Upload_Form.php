<?php

class Rack_Point_Block_Adminhtml_Import_Upload_Form extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareLayout()
    {
        $form = new Varien_Data_Form(array(
            'method'    => 'post',
            'enctype'   => 'multipart/form-data',
            'action'    => $this->getUrl('*/*/preview')
        ));
        
        $form->addField('data_file', 'file', array(
            'label'     => $this->__('Data file:'),
            'required'  => true,
            'name'      => 'data_file',
        ));
        
        $form->addField('submit', 'submit', array(
            'label' => '　',
            'value' => $this->__('Upload'),
            'class' => 'button save'
        ));
        
        $form->setUseContainer(true);
        $this->setForm($form);
        
        parent::_prepareLayout();
    }
}