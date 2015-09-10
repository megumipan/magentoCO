<?php

/**
 * @license http://opensource.org/licenses/osl-3.0.php OSL-3.0
 * @author Yoshihisa AMAKATA <amakata@flatz.jp>
 * @copyright (c) 2013, FLATz Inc.
 */
class FLATz_Base_Model_Tax_Calculation extends Mage_Tax_Model_Calculation {

    public function calcTaxAmount($price, $taxRate, $priceIncludeTax = false, $round = true) {
        $taxRate = $taxRate / 100;

        if ($priceIncludeTax) {
            $amount = $price * (1 - 1 / (1 + $taxRate));
        } else {
            $amount = $price * $taxRate;
        }

        if (!$priceIncludeTax) {
            if (Mage::helper('flatz_base')->canUseJpy()) {
                $method = Mage::getStoreConfig('tax/calculation/round');
                switch ($method) {
                    case 'ceil':
                        $amount = ceil($amount);
                        break;
                    case 'floor':
                        $amount = floor($amount);
                        break;
                    case 'round':
                        $amount = $this->round($amount);
                        break;
                }
                return $amount;
            }
        }

        if ($round) {
            return $this->round($amount);
        }

        return $amount;
    }

}
