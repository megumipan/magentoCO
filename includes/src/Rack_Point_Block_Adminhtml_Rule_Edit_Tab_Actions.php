<?php

class Rack_Point_Block_Adminhtml_Rule_Edit_Tab_Actions extends Mage_Adminhtml_Block_Widget_Form implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    /**
     * Prepare content for tab
     *
     * @return string
     */
    public function getTabLabel()
    {
        return Mage::helper('rackpoint')->__('Actions');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return Mage::helper('rackpoint')->__('Actions');
    }

    /**
     * Returns status flag about this tab can be showen or not
     *
     * @return true
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Returns status flag about this tab hidden or not
     *
     * @return true
     */
    public function isHidden()
    {
        return false;
    }

    protected function _prepareForm()
    {
        $model = Mage::registry('current_point_rule');

        $form = new Varien_Data_Form();

        $form->setHtmlIdPrefix('rule_');

        $fieldset = $form->addFieldset('action_fieldset', array('legend'=>Mage::helper('rackpoint')->__('Apply Points Using the Following Information')));

        $fieldset->addField('simple_action', 'select', array(
            'label'     => Mage::helper('rackpoint')->__('Apply'),
            'name'      => 'simple_action',
            'options'    => array(
                'by_percent' => Mage::helper('rackpoint')->__('By Percentage of the Original Price'),
                'by_fixed' => Mage::helper('rackpoint')->__('By Fixed Amount'),
            ),
        ));

        $fieldset->addField('point_amount', 'text', array(
            'name' => 'point_amount',
            'required' => true,
            'class' => 'validate-not-negative-number',
            'label' => Mage::helper('rackpoint')->__('Point Amount'),
        ));

        $fieldset->addField('stop_rules_processing', 'select', array(
            'label'     => Mage::helper('rackpoint')->__('Stop Further Rules Processing'),
            'title'     => Mage::helper('rackpoint')->__('Stop Further Rules Processing'),
            'name'      => 'stop_rules_processing',
            'options'    => array(
                '1' => Mage::helper('rackpoint')->__('Yes'),
                '0' => Mage::helper('rackpoint')->__('No'),
            ),
        ));

        $form->setValues($model->getData());

        if ($model->isReadonly()) {
            foreach ($fieldset->getElements() as $element) {
                $element->setReadonly(true, true);
            }
        }

        $this->setForm($form);

        return parent::_prepareForm();
    }
}
