<?php

/**
 * @license http://opensource.org/licenses/osl-3.0.php OSL-3.0
 * @author Yoshihisa AMAKATA <amakata@flatz.jp>
 * @copyright (c) 2013, FLATz Inc.
 */
class FLATz_Base_Model_Pdf_Font_File {

    const XML_PATH_PDF_FONT_PATH = 'flatz_base_pdf/font';
    const PDF_FONT_REGULER = 'reguler';
    const PDF_FONT_BOLD = 'bold';
    const PDF_FONT_ITALIC = 'italic';

    protected $_fontPathStr;

    public function __construct($type, $store = null) {
        
        $this->_fontPathStr = Mage::getStoreConfig(self::XML_PATH_PDF_FONT_PATH . '/' . $type , $store);
    }

    public function getPath() {
        return $this->_fontPathStr;
    }

}
