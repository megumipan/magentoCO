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

class IrvineSystems_Awardpoints_Block_AboutAwardpoints
    extends Mage_Adminhtml_Block_Abstract
    implements Varien_Data_Form_Element_Renderer_Interface
{
	public function render(Varien_Data_Form_Element_Abstract $element)
	{
		$html1 = <<<HTML
<div style="background:url('
HTML;
		$html2 = $this->getSkinUrl('images/irvinesystems/award_points.png');
		$html3 = <<<HTML
				') no-repeat scroll 14px 14px #EAF0EE;border:1px solid #CCCCCC;margin-bottom:10px;padding:10px 5px 5px 105px;">
<h2 style="text-align: justify; color:#e46b00;">Reward your Customer and improve your sales with many sweets Points!</h2>
<p style="text-align: justify;">You can now practically improve your sales and the motivation of your customers with this great extension!<br />
This module will allow your customers to gather points according to your directives; your customer will have access to points according to the products, the shopping carts, your personal rules for campaign, for website registration, for friends' referral, for their review and much, much more!<br />
All the point gathered by the customer can be used as discount and you can freely and fully decide all the rules on how these points can be spent!<br /><br />

Many other shopping points extensions are available for Magento, each one featuring very specific properties. In order to have a complete system for reward and shopping points it would be required to purchase and install many of these extensions and make it work together.<br />
However now this is not any more necessary, Award Points is the first and unique extension to feature all the advantage of any other extension plus unique and exclusive features.<br />
Finally you can have a truly complete system with only one Magento extension.</p>
<p><em>Irvine Mage</em></p>
</div>
HTML;
	$html = $html1 . $html2 . $html3;
		return $html;
	}
}