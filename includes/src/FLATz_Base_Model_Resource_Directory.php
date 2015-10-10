<?php

/**
 * @license http://opensource.org/licenses/osl-3.0.php OSL-3.0
 * @author Yoshihisa AMAKATA <amakata@flatz.jp>
 * @copyright (c) 2013, FLATz Inc.
 */
class FLATz_Base_Model_Resource_Directory extends Mage_Core_Model_Mysql4_Abstract {

    /**
     * コンストラクタ
     */
    public function _construct() {
        $this->_init('flatz_base/directory', 'currency_id');
    }

}