<?php
/*
 * Irvine Systems Shipping Japan Ymt
 *
 * NOTICE OF LICENSE
 * This source file is subject to the Revised BSD License which is bundled with
 * this package in the files LICENSE.txt and LICENSE.html.
 *
 * @category	Magento Shipping Extension
 * @package		IrvineSystems_Yamato
 * @author Irvine Systems, Inc.
 * @copyright Copyright (c) 2015, Irvine Systems, Inc. (http://irvinesystems.co.jp/)
 * @license     Revised BSD License
 */

class IrvineSystems_Yamato_Block_AboutYamato
	extends Mage_Adminhtml_Block_Abstract
	implements Varien_Data_Form_Element_Renderer_Interface
{

	public function render(Varien_Data_Form_Element_Abstract $element)
	{
		$html1 = <<<HTML
<div style="background:url('
HTML;
		$html2 = $this->getSkinUrl('images/irvinesystems/shipping_japan_ymt.png');
		$html3 = <<<HTML
				') no-repeat scroll 14px 14px #EAF0EE;border:1px solid #CCCCCC;margin-bottom:10px;padding:10px 5px 5px 105px;">
<h2 style="text-align: justify; color:#e46b00;">The Only Fully Operative Magento extension for Yamato Transport Co.,Ltd. Shipping!</h2>
<p style="text-align: justify;">Shipping Japan Ymt is a part of Irvine Mage’s exclusive Magento extension series "Shipping Japan" which specifically supports shipping carriers in Japan.<br />
Shipping Japan Ymt makes all the major shipping services of Yamato Transport available at your store!<br />
The extension offers a great variety of "optional" settings to personalize the shipping rates at your store to reflect every detail of your needs and policies. Also, your store data will be fully compatible with Yamato Transport’s shipping slips using the extension.<br />
Shipping Japan Ymt is fully customized with Yamato Transport and integrated in the module. All the detailed specifications of the carrier’s services are taken into consideration to achieve the absolute accuracy of the system.</p>
<p><em>Irvine Mage</em></p>
</div>
HTML;
	$html = $html1 . $html2 . $html3;
		return $html;
	}
}
