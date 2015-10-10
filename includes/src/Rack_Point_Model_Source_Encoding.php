<?php

class Rack_Point_Model_Source_Encoding
{
    public function toOptionArray()
    {
        return array(
            'SJIS'      => 'SJIS',
            'EUC-JP'    => 'EUC-JP',
            'UTF-8'     => 'UTF-8',
        );
    }
}