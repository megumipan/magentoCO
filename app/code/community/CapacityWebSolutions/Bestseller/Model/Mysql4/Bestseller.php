<?php
/***************************************************************************
	@extension	: Best Seller Products.
	@copyright	: Copyright (c) 2015 Capacity Web Solutions.
	( http://www.capacitywebsolutions.com )
	@author		: Capacity Web Solutions Pvt. Ltd.
	@support	: magento@capacitywebsolutions.com	
***************************************************************************/

class CapacityWebSolutions_Bestseller_Model_Mysql4_Bestseller extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        $this->_init('bestseller/bestseller', 'bestseller_id');
    }
}