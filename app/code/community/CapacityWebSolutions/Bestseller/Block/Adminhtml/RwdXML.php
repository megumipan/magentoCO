<?php
/***************************************************************************
	@extension	: Bestseller Product.
	@copyright	: Copyright (c) 2015 Capacity Web Solutions.
	( http://www.capacitywebsolutions.com )
	@author		: Capacity Web Solutions Pvt. Ltd.
	@support	: magento@capacitywebsolutions.com	
***************************************************************************/

class CapacityWebSolutions_Bestseller_Block_Adminhtml_RwdXML
    extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
		return '<div style="background:#efefef;border:1px solid #d8d8d8;padding:10px;margin-bottom:10px;">
		<span>&lt;reference name="content"&gt;	</br>&lt;block type="bestseller/bestseller" name="bestseller" template="bestseller/bestseller-responsive.phtml"/&gt;</br>&lt;/reference&gt;</span>	
		</div>';
	
    }
	
}