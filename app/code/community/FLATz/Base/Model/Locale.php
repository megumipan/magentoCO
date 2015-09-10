<?php

/**
 * @license http://opensource.org/licenses/osl-3.0.php OSL-3.0
 * @author Yoshihisa AMAKATA <amakata@flatz.jp>
 * @copyright (c) 2013, FLATz Inc.
 */
class FLATz_Base_Model_Locale extends Mage_Core_Model_Locale {

    /**
     * コンストラクタ
     * @param Mage_Core_Model_Locale $locale
     */
    public function __construct($locale = null) {
        parent::__construct($locale);
    }

    /**
     * Create Mage_Core_Model_Locale_Currency object for current locale
     *
     * @param   string $currency
     * @return  Mage_Core_Model_Locale_Currency
     */
    public function currency($currency) {
        Varien_Profiler::start('locale/currency');
        if (!isset(self::$_currencyCache[$this->getLocaleCode()][$currency])) {
            try {
                $currencyObject = new Zend_Currency($currency, $this->getLocale());
                if ($currency === 'JPY' && Mage::helper('flatz_base')->canUseJpy()) {
                    $options = Mage::helper('flatz_base')->getOptions(array());
                    $currencyObject->setFormat(array('precision' => $options["precision"]));
                }
            } catch (Exception $e) {
                $currencyObject = new Zend_Currency($this->getCurrency(), $this->getLocale());
                $options = array(
                    'name' => $currency,
                    'currency' => $currency,
                    'symbol' => $currency
                );
                $currencyObject->setFormat($options);
            }

            self::$_currencyCache[$this->getLocaleCode()][$currency] = $currencyObject;
        }
        Varien_Profiler::stop('locale/currency');
        return self::$_currencyCache[$this->getLocaleCode()][$currency];
    }

    public function getJsPriceFormat()
    {
            // For JavaScript prices
            $parentFormat=parent::getJsPriceFormat();
            $options = Mage::helper('flatz_base')->getOptions(array());
            if (isset($options["precision"]))
            {
                    $parentFormat["requiredPrecision"] = $options["precision"];
                    $parentFormat["precision"] = $options["precision"];
            }

            return $parentFormat;
    }
}
