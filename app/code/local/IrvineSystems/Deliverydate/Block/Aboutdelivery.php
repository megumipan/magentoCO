<?php
/*
 * Irvine Systems Delivery Date Optimum
 *
 * NOTICE OF LICENSE
 * This source file is subject to the Revised BSD License which is bundled with
 * this package in the files LICENSE.txt and LICENSE.html.
 *
 * @category	Magento Catalog Extension
 * @package		IrvineSystems_Deliverydate
 * @author Irvine Systems, Inc.
 * @copyright Copyright (c) 2015, Irvine Systems, Inc. (http://irvinesystems.co.jp/)
 * @license     Revised BSD License
 */

class IrvineSystems_Deliverydate_Block_Aboutdelivery
	extends Mage_Adminhtml_Block_Abstract
	implements Varien_Data_Form_Element_Renderer_Interface
{

	public function render(Varien_Data_Form_Element_Abstract $element)
	{
		$html1 = <<<HTML
<div style="background:url('
HTML;
		$html2 = $this->getSkinUrl('images/irvinesystems/delivery_date_optimum.png');
		$html3 = <<<HTML
				') no-repeat scroll 14px 14px #EAF0EE;border:1px solid #CCCCCC;margin-bottom:10px;padding:10px 5px 5px 105px;">
<h2 style="text-align: justify; color:#e46b00;">Let your customer choose when they can receive their shipment!</h2>
<p style="text-align: justify;">This great and fully performing extension will grant your customer with the option to choose a delivery date and time for their purchase. Moreover they can also add additional delivery comment for further personalize their shipment.<br />
Which method can feature the delivery option, which dates and which time can fully be configured by the store owner with fast and simple clicks in the admin panel option.<br />
Improve your customer experience in your store allowing your customer to freely choose them shipping schedule!!</p>
<p><em>Irvine Mage</em></p>
</div>
HTML;
	$html = $html1 . $html2 . $html3;
		return $html;
	}
}
