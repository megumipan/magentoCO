<?php
/**
 * @license http://opensource.org/licenses/osl-3.0.php OSL-3.0
 * @author Yoshihisa AMAKATA <amakata@flatz.jp>
 * @copyright (c) 2013, FLATz Inc.
 */
class FLATz_Base_Block_Adminhtml_Review_Main extends Mage_Adminhtml_Block_Review_Main 
{
    /**
     * コンストラクタ
     */
    public function __construct()
    {
        parent::__construct();
        if (Mage::app()->getLocale()->getLocaleCode() != 'ja_JP') {
            return;
        }
        $this->_addButtonLabel = Mage::helper('review')->__('Add New Review');

        $this->_controller = 'review';

        // lookup customer, if id is specified
        $customerId = $this->getRequest()->getParam('customerId', false);
        $customerName = '';
        if ($customerId) {
            $customer = Mage::getModel('customer/customer')->load($customerId);
            $customerName = $customer->getLastname() . ' ' . $customer->getFirstname();
        }

        if( Mage::registry('usePendingFilter') === true ) {
            if ($customerName) {
                $this->_headerText = Mage::helper('review')->__('Pending Reviews of Customer `%s`', $customerName);
            } else {
                $this->_headerText = Mage::helper('review')->__('Pending Reviews');
            }
            $this->_removeButton('add');
        } else {
            if ($customerName) {
                $this->_headerText = Mage::helper('review')->__('All Reviews of Customer `%s`', $customerName);
            } else {
                $this->_headerText = Mage::helper('review')->__('All Reviews');
            }
        }
    }
}
