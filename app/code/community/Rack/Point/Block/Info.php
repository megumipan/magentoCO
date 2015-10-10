<?php
/**
 * Created by Celtic Corporation
 * User: ndlinh
 * Date: 04/03/2012
 * Time: 15:41
 */
class Rack_Point_Block_Info extends Mage_Core_Block_Template
{
    public function _construct()
    {
        parent::_construct();
        $this->setTemplate('rackpoint/info.phtml');
    }

    /**
     * Get current quote object
     *
     * @return Mage_Sales_Model_Quote
     */
    public function getQuote()
    {
        return Mage::getSingleton('checkout/session')->getQuote();
    }

    /**
     * Get calculation debug info
     *
     * @return array
     */
    public function getCalculationInfo()
    {
        $obj = $this->getQuote()->getPointCalculationInfo();
        return  $obj ? $obj : array();
    }

    public function formatPrice($price)
    {
        return Mage::app()->getStore()->formatPrice($price);
    }

    public function formatPoint($point, $decimal = 0)
    {
        return number_format($point, $decimal);
    }

    public function isIncludeTax()
    {
        return Mage::getStoreConfig('rackpoint/calculate/prod_includes_tax') == '1' ? true : false;
    }

    public function _toHtml()
    {
        if (Mage::getStoreConfig('rackpoint/config/enable_show_info') == 1) {
            return parent::_toHtml();
        }

        return '';
    }
}
