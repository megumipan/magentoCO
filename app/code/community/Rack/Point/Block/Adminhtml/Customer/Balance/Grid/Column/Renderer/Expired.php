<?php

class Rack_Point_Block_Adminhtml_Customer_Balance_Grid_Column_Renderer_Expired
    extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    /**
     * Render currency
     *
     * @param   Varien_Object $row
     * @return  string
     */
    protected function _getValue(Varien_Object $row)
    {
        if ($row->isExpired() == true) {
            return $this->__('Expired');
        } else {
            return $this->__('Not yet');
        }
    }
}
