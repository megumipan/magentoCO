<?php

/**
 * @license http://opensource.org/licenses/osl-3.0.php OSL-3.0
 * @author Yoshihisa AMAKATA <amakata@flatz.jp>
 * @copyright (c) 2013, FLATz Inc.
 */
class FLATz_Base_Helper_Data extends Mage_Core_Helper_Abstract {

    /**
     * Retrieve args node of observer config
     * 
     * @param Varien_Event_Observer $observer
     * @return array $observerArgs
     */
    public function getObserverArgs(Varien_Event_Observer $observer, $callingClass, $callingMethod) {

        /**
         * Define vars
         */
        $usedObservers = array();
        $observerArgs = array();
        $eventObservers = array();

        /**
         * Load Magento config
         */
        $config = Mage::getConfig();

        /**
         * Retrieve all observers attached to the current observer's event
         */
        $eventObservers = (array) $config->getXpath('//events/' . $observer->getEvent()->getName() . '/observers/*');

        /**
         * Retrieve all XML nodes of the current observer (including <args>!)
         * and populate $usedObservers with observers that:
         * - call the same class and method than the $observer passed as arguments for this function
         * - have an <args> node declared in config
         */
        foreach ($eventObservers as $eventObserver) {
            $className = $config->getModelClassName($eventObserver->class);
            $method = $eventObserver->method;
            $args = (bool) $eventObserver->args;
            if ($className == $callingClass && $method == $callingMethod && $args) {
                $usedObservers[] = $eventObserver;
            }
        }

        /**
         * Create array of args
         */
        foreach ($usedObservers as $usedObserver) {
            $args = (array) $usedObserver->args;
            foreach ($args as $name => $value) {
                $observerArgs[$name] = $value;
            }
        }

        $args = new Varien_Object;
        $args->setData($observerArgs);

        return $args;
    }
    
    /**
     * 
     * @return boolean
     */
    public function canUseJpy() {
        $store = Mage::app()->getStore();
        if ($store->getCurrentCurrencyCode() == 'JPY') {
            $available = $store->getAvailableCurrencyCodes();

            if (in_array('JPY', $available)) {
                return true;
            }
        }
        return false;
    }

    protected $_options = array();

    public function getOptions($options = array()) {
        if (!$this->_options) {
            $store = Mage::app()->getStore();
            if ($store->getCurrentCurrencyCode() == 'JPY') {
                if (Mage::getStoreConfig('flatz_base_japanese/currency/position') == Zend_Currency::RIGHT) {
                    $this->_options['position'] = (int) Mage::getStoreConfig('flatz_base_japanese/currency/position');
                    $this->_options['symbol'] = Mage::helper('flatz_base')->__('Yen');
                }

                $this->_options['precision'] = 0;
            }
        }
        return array_merge($options, $this->_options);
    }
}
