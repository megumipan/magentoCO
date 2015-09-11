<?php
class Maruweb_Digitalcheck_Model_Source_Limitdate {
    public function toOptionArray() {
        $return_array = array();
        $return_array[] = array(
               'value' => '',
               'label' => Mage::helper('adminhtml')->__('-- Please Select --')
        );
        for ($i=1; $i<=30; $i++) {
        	$return_array[] = array('value' => $i, 'label' => Mage::helper('digitalcheck')->__('%sDays', $i));
        }
        return $return_array;
    }
}
