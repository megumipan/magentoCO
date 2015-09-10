<?php

/**
 * @license http://opensource.org/licenses/osl-3.0.php OSL-3.0
 * @author Yoshihisa AMAKATA <amakata@flatz.jp>
 * @copyright (c) 2013, FLATz Inc.
 */
class FLATz_Base_Model_Sales_Order_Pdf_Creditmemo extends Mage_Sales_Model_Order_Pdf_Creditmemo {

    protected function _setFontRegular($object, $size = 7) {
        $fontFile = Mage::getModel('flatz_base/pdf_font_file', FLATz_Base_Model_Pdf_Font_File::PDF_FONT_REGULER);
        $font = Zend_Pdf_Font::fontWithPath($fontFile->getPath());
        $object->setFont($font, $size);
        return $font;
    }

    protected function _setFontBold($object, $size = 7) {
        $fontFile = Mage::getModel('flatz_base/pdf_font_file', FLATz_Base_Model_Pdf_Font_File::PDF_FONT_BOLD);
        $font = Zend_Pdf_Font::fontWithPath($fontFile->getPath());
        $object->setFont($font, $size);
        return $font;
    }

    protected function _setFontItalic($object, $size = 7) {
        $fontFile = Mage::getModel('flatz_base/pdf_font_file', FLATz_Base_Model_Pdf_Font_File::PDF_FONT_ITALIC);
        $font = Zend_Pdf_Font::fontWithPath($fontFile->getPath());
        $object->setFont($font, $size);
        return $font;
    }
    
     
}
