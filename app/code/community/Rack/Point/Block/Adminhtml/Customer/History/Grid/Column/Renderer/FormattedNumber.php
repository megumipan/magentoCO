<?php
/**
 * Created by Celtic Corporation
 * User: ndlinh
 * Date: 2013-06-11
 *
 * Copyright ©2013 Celtic Corporation. All Rights Reserved.
 */
class Rack_Point_Block_Adminhtml_Customer_History_Grid_Column_Renderer_FormattedNumber extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Number
{
    /**
     * Returns value of the row
     *
     * @param Varien_Object $row
     * @return mixed|string
     */
    protected function _getValue(Varien_Object $row)
    {
        $data = parent::_getValue($row);
        if (!is_null($data)) {
            $value = $data * 1;
            $sign = (bool)(int)$this->getColumn()->getShowNumberSign() && ($value > 0) ? '+' : '';

            $decimal = $this->getColumn()->getDecimal() ? $this->getColumn()->getDecimal() : 0;
            $decPoint = $this->getColumn()->getDecimalPoint() ? $this->getColumn()->getDecimalPoint() : '.';
            $thousandSep = $this->getColumn()->getThousandSep() ? $this->getColumn()->getThousandSep() : ',';

            $value =  $value ? number_format($value, $decimal, $decPoint, $thousandSep) : '0'; // fixed for showing zero in grid
            if ($sign) {
                $value = $sign . $value;
            }

            return $value;
        }
        return $this->getColumn()->getDefault();
    }
}