<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Bundle
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Bundle Option Type Source Model
 *
 * @category   Mage
 * @package    Mage_Bundle
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class FLATz_Base_Model_Bundle_Source_Option_Type  extends Mage_Bundle_Model_Source_Option_Type
{
    public function toOptionArray()
    {
        $types = array();

        foreach (Mage::getConfig()->getNode(self::BUNDLE_OPTIONS_TYPES_PATH)->children() as $type) {
            $labelPath = self::BUNDLE_OPTIONS_TYPES_PATH . '/' . $type->getName() . '/label';
            $types[] = array(
// bugfix -　翻訳が当たるように修正した                
//                'label' => (string) Mage::getConfig()->getNode($labelPath),
                'label' => Mage::helper('bundle')->__((string) Mage::getConfig()->getNode($labelPath)),
                'value' => $type->getName()
            );
        }

        return $types;
    }
}
