<?php/***************************************************************************	@extension	: Best Seller Products.	@copyright	: Copyright (c) 2015 Capacity Web Solutions.	( http://www.capacitywebsolutions.com )	@author		: Capacity Web Solutions Pvt. Ltd.	@support	: magento@capacitywebsolutions.com	***************************************************************************/class CapacityWebSolutions_Bestseller_Model_System_Config_Source_Bundleproducts {	/**	 * Options getter	 *	 * @return array	 */	public function toOptionArray()    {		 $array=array(		  array(		  'value'=>'1',		  'label'=> 'Only Child' 		   ),		  array(		  'value'=>'2',		  'label'=> 'Only Parent' 		   ),		  array(		  'value'=>'3',		  'label'=> 'Both' 		   ),		  		  );		return $array;    }	/**	 * Get options in "key-value" format	 *	 * @return array	 */	public function toArray()	{		$options = array('1'=>'Only Child','2'=>'Only Parent','3'=>'Both');		return $options;	}}