<?php
/*
 * Irvine Systems Award Points
 *
 * NOTICE OF LICENSE
 * This source file is subject to the Revised BSD License which is bundled with
 * this package in the files LICENSE.txt and LICENSE.html.
 *
 * @category	Magento Sale Extension
 * @package		IrvineSystems_AwardPoints
 * @author Irvine Systems, Inc.
 * @copyright Copyright (c) 2015, Irvine Systems, Inc. (http://irvinesystems.co.jp/)
 * @license     Revised BSD License
 */

class IrvineSystems_Awardpoints_Block_Productlist extends Mage_Catalog_Block_Product_List
{
    /**
     * PriceHtml Block override
     * 
     */
    public function getPriceHtml($product, $displayMinimalPrice = false, $idSuffix = '')
    {
		// Get Parent Price Html
		$returnHtml = parent::getPriceHtml($product, $displayMinimalPrice = false, $idSuffix = '');

		// Check if point for the Product are available
		$productPoints =  Mage::helper('awardpoints/data')->getProductPoints($product);
		$pointsHtml = null;
		if($productPoints){
			// Create Points HTML
			$helper = Mage::helper('awardpoints');
			$pointsHtml ='
			    <div class="price-box">
					<img src='.$this->getSkinUrl('images/AwardPoints/AwardPoints.png').' alt="" />
					<span>'.$helper->__("Points: ").sprintf("<span id='award-points'>%d</span>", $productPoints).'</span>
				</div>
			';
		}
		// If point html is available add it to the Parent Price Html
		if ($pointsHtml) $returnHtml .= $pointsHtml;
		// Return the Html Code
		return $returnHtml;
    }
}