<?php
/*
 * Irvine Systems Shipping Japan Jp
 *
 * NOTICE OF LICENSE
 * This source file is subject to the Revised BSD License which is bundled with
 * this package in the files LICENSE.txt and LICENSE.html.
 *
 * @category	Magento Shipping Extension
 * @package		IrvineSystems_JapanPost
 * @author Irvine Systems, Inc.
 * @copyright Copyright (c) 2015, Irvine Systems, Inc. (http://irvinesystems.co.jp/)
 * @license     Revised BSD License
 */

class IrvineSystems_JapanPost_Block_AboutJapanpost
	extends Mage_Adminhtml_Block_Abstract
	implements Varien_Data_Form_Element_Renderer_Interface
{

	public function render(Varien_Data_Form_Element_Abstract $element)
	{
		$html1 = <<<HTML
<div style="background:url('
HTML;
		$html2 = $this->getSkinUrl('images/irvinesystems/shipping_japan_jp.png');
		$html3 = <<<HTML
				') no-repeat scroll 14px 14px #EAF0EE;border:1px solid #CCCCCC;margin-bottom:10px;padding:10px 5px 5px 105px;">
<h2 style="text-align: justify; color:#e46b00;">The Only Fully Operative Magento extension for Japan Post Shipping!</h2>
<p style="text-align: justify;">Shipping Japan Jp is a part of Irvine Mage’s exclusive Magento extension series "Shipping Japan" which specifically supports shipping carriers in Japan.<br />
Shipping Japan Jp makes all the major shipping services of Japan post available at your store!<br />
The extension offers a great variety of "optional" settings to personalize the shipping rates at your store to reflect every detail of your needs and policies. Also, your store data will be fully compatible with Japan Post’s shipping slips using the extension.<br />
Shipping Japan Jp is fully customized with Japan Post and integrated in the module. All the detailed specifications of the carrier’s services are taken into consideration to achieve the absolute accuracy of the system.</p>
<p><em>Irvine Mage</em></p>
</div>
HTML;
	$html = $html1 . $html2 . $html3;
		return $html;
	}
}
