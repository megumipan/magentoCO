<?php
class Maruweb_Digitalcheck_Model_Source_Cvstype {
    public function toOptionArray() {
        return array(
                array('value' => '1', 'label' => Mage::helper('digitalcheck')->__('LoppiPayment')),
                array('value' => '2', 'label' => Mage::helper('digitalcheck')->__('SevenElevenPayment')),
                array('value' => '3', 'label' => Mage::helper('digitalcheck')->__('FamilyMartPayment')),
                array('value' => '73', 'label' => Mage::helper('digitalcheck')->__('CVSOnlinePayment')),
                );
    }

    public function getCvsType($cvsType) {
        $code = "";
        switch ($cvsType) {
            case '00C016' :
                $code = Mage::helper('paygent')->__('SeicoMart');
                break;
            case '00C002' :
                $code = Mage::helper('paygent')->__('Lawson');
                break;
            case '00C004' :
                $code = Mage::helper('paygent')->__('MiniStop');
                break;
            case '00C006' :
                $code = Mage::helper('paygent')->__('Sunkus');
                break;
            case '00C007' :
                $code = Mage::helper('paygent')->__('CircleK');
                break;
            case '00C014' :
                $code = Mage::helper('paygent')->__('DailyYamazaki');
                break;
            case '00C001' :
                $code = Mage::helper('paygent')->__('SevenEleven');
                break;
            case '00C005' :
                $code = Mage::helper('paygent')->__('FamilyMart');
                break;
        }

        return $code;
    }
}
