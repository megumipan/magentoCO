<?php

class Rack_Point_Block_Adminhtml_Rule_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{

    public function __construct()
    {
        $this->_objectId = 'id';
        $this->_controller = 'adminhtml_rule';
        $this->_blockGroup = 'rackpoint';

        parent::__construct();

        $this->_updateButton('save', 'label', Mage::helper('rackpoint')->__('Save Rule'));
        $this->_updateButton('delete', 'label', Mage::helper('rackpoint')->__('Delete Rule'));

        $rule = Mage::registry('current_point_rule');

        if (!$rule->isDeleteable()) {
            $this->_removeButton('delete');
        }

        if (!$rule->isReadonly()) {
            $this->_addButton('save_apply', array(
                'class'=>'save',
                'label'=>Mage::helper('rackpoint')->__('Save and Apply'),
                'onclick'=>"$('rule_auto_apply').value=1; editForm.submit()",
            ));
            $this->_addButton('save_and_continue', array(
                'label'     => Mage::helper('catalogrule')->__('Save and Continue Edit'),
                'onclick'   => 'saveAndContinueEdit()',
                'class' => 'save'
            ), 10);
            $this->_formScripts[] = " function saveAndContinueEdit(){ editForm.submit($('edit_form').action + 'back/edit/') } ";
        } else {
            $this->_removeButton('reset');
            $this->_removeButton('save');
        }
    }

    public function getHeaderText()
    {
        $rule = Mage::registry('current_point_rule');
        if ($rule->getRuleId()) {
            return Mage::helper('rackpoint')->__("Edit Rule '%s'", $this->htmlEscape($rule->getName()));
        }
        else {
            return Mage::helper('rackpoint')->__('New Rule');
        }
    }

}
