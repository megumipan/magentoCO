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

class IrvineSystems_Sagawa_Adminhtml_ParcelsController extends IrvineSystems_Sagawa_Controller_Abstract
{
	// Slips mode target
	protected $_target ='parcels';

    /**
     * Slips area entry point
     * Render Layout
     * 
     */
	public function indexAction()
	{
		// Initialize Action Sets
		$this->_initAction()
			->_addContent($this->getLayout()->createBlock('sagawa/adminhtml_parcels'))
			->renderLayout();
	}
}