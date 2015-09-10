<?php
/*
 * Irvine Systems Shipping Japan Sgw
 *
 * NOTICE OF LICENSE
 * This source file is subject to the Revised BSD License which is bundled with
 * this package in the files LICENSE.txt and LICENSE.html.
 *
 * @category	Magento Shipping Extension
 * @package		IrvineSystems_Sagawa
 * @author Irvine Systems, Inc.
 * @copyright Copyright (c) 2015, Irvine Systems, Inc. (http://irvinesystems.co.jp/)
 * @license     Revised BSD License
 */
 
class IrvineSystems_Sagawa_Resource_Data
{
	/**
	* Cart Dimension Packer
	*
	* The array return is structure as follow:
	* ['Vol_Length']	= Volumetric Length			= The longest measure between the three
	* ['Vol_Circum'] 	= Volumetric Circumference	= The sum of the 2 shortest measures multiply by 2
	* ['Vol_Volume']	= Volumetric Volume			= Volumetric Length + Volumetric Circumference
	* ['Vol_Basic']		= Volumetric Basic			= L+W+H
	* ['Raw_Sizes']		= Raw Dimensions			= array(H,W,L)
	*
    * @return Array
	**/
	public function packStrut()
	{
		// Get the information for the products in the shopping cart
		$quote = Mage::getSingleton('checkout/session')->getQuote();
		$items = $quote->getItemsCollection()->getItems();
		
		// Check if we have items
		// If it is empty it means we are doing an order from the Admin Panel therefore get the correct Data
		If (empty($items)){
			$quote = Mage::getSingleton('adminhtml/session_quote')->getQuote();
			$items = $quote->getAllItems();
		}

		// Define Row Container Array
		$rowsCont = array();

		// Compact each product row (product by quantity) and add it to the _rowsCont array
		foreach($items as $item) {
			// Pack the current row
			$rowDim = $this->consRow($item);
			// insert the consolidated dimension in the Rows Container
			array_push($rowsCont, $rowDim);
		}
		
		// Pack Shopping Cart
		$totRows = count($rowsCont);
		$currentRow = 1;
		foreach($rowsCont as $row) {
			// Check if the cart has only one row or if we are at the first row
			if ($totRows == 1 || $currentRow == 1) {
				// If it is only one or we are at the first row we simply need to set the final Values
				// with the returned row values
				$cartDim = $row;
			} else {
				// Increase the smaller cart dimension with the row smaller dimension
				$cartDim[0] += $row[0];
				// Update the other dimension if the new dimension are greater
				if ($row[1] > $cartDim[1]) $cartDim[1] = $row[1];
				if ($row[2] > $cartDim[2]) $cartDim[2] = $row[2];

			}
			// Update Counter
			$currentRow ++;
		}

		// Sort the Cart dimension array for maintain a cresent order
		sort($cartDim);
	
		// Set Calculation arams
		$v1 = $cartDim[0];
		$v2 = $cartDim[1];
		$v3 = $cartDim[2];

		// Set all the values needed for carriers
		$vol_Length = $v3; 						// EMS req.: Greatest value within the 3 values (called Length)
		$vol_Circum = ($v1+$v1)*2; 				// EMS req.: Circum using the smaler and the medium value
		$vol_Volume = $vol_Length+$vol_Circum;	// EMS req.: The Sum of Length and Circum
		$vol_Basic = $v1+$v2+$v3; 				// Parcel Size used for Yuu-Pack in Japan
		$raw_Sizes = array($v1,$v2,$v3); 		// Raw Sizes

		// Set final output Array
		$result = array(
	        'Vol_Length'	=> $vol_Length,
	        'Vol_Circum'	=> $vol_Circum,
	        'Vol_Volume'	=> $vol_Volume,
	        'Vol_Basic'		=> $vol_Basic,
	        'Raw_Sizes'		=> $raw_Sizes
	    );

		// Return the consolidation result
		return $result;
	}

	/**
	* Consolidate the Volume on the given Shopping Cart Row
	*	
    * @param $item Shopping Cart $item Object
    * @return Array the Grupped dimension of the Row
	**/
	public function consRow($item)
	{
		// Get the information of the currenct product
		$product = Mage::getModel('catalog/product');
		$product = $product->load($item->getData('product_id'));
		$depth = $product->getData('pkg_depth');
		$height = $product->getData('pkg_height');
		$width = $product->getData('pkg_width');

		// Set Row dimension Array
		$rowDim = array($depth, $height, $width);
			
		// sort the row dimension for maintain a crescent order
		sort($rowDim);

		// Multiply the smaller dimension by the row quantity
		$rowDim[0] = $rowDim[0]*$item->getQty();

		// Sort the array for maintain a cresent order
		sort($rowDim);

		// return the dimension
		return $rowDim;
	}
}