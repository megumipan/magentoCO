<?php

/**
 * @license http://opensource.org/licenses/osl-3.0.php OSL-3.0
 * @author Yoshihisa AMAKATA <amakata@flatz.jp>
 * @copyright (c) 2013, FLATz Inc.
 */
class FLATz_Base_Model_Directory extends Mage_Core_Model_Abstract {

    /**
     * コンストラクタ
     */
    public function _construct() {
        $this->_init('flatz_base/directory');
    }

    /**
     * 
     * @return \FLATz_Base_Model_Directory
     */
    protected function _afterSave() {
        if ($this->hasData('store_labels')) {
            $labelCollection = Mage::getResourceModel('flatz_base/directory_label_collection');

            $labelCollection->addCurrencyIdToFilter($this->getId());
            //remove current labels
            foreach ($labelCollection->getItems() as $label) {
                $label->delete();
            }

            $labelCollection->resetData();
            //save new labels
            $labels = $this->getData('store_labels');
            foreach ($labels as $storeId => $label) {
                if (empty($label)) {
                    continue;
                }
                $labelObj = Mage::getModel('flatz_base/directory_label');
                $labelObj->setData(array(
                    'currency_id' => $this->getId(),
                    'store_id' => $storeId,
                    'label' => $label,
                ));
                $labelCollection->addItem($labelObj);
            }

            $labelCollection->save();
        }
        return parent::_afterSave();
    }

    /**
     * 
     * @return void
     */
    public function loadLabels() {
        if (!$this->getId()) {
            return;
        }
        $labelCollection = Mage::getResourceModel('flatz_base/currency_label_collection');

        $labelCollection->addCurrencyIdToFilter($this->getId());
        $labels = array();
        foreach ($labelCollection->getItems() as $labelObj) {
            $labels[$labelObj->getStoreId()] = $labelObj->getLabel();
        }

        if ($labels) {
            $this->setData('store_labels', $labels);
        }
        $storeId = Mage::app()->getStore()->getId();
        if (isset($labels[$storeId])) {
            $this->setLabel($labels[$storeId]);
        } else {
            $this->setLabel($labels[0]);
        }
    }

}