<?php

class Rack_Point_Model_MbCsvReader extends Rack_Point_Model_CsvReader
{
    /**
     * CSV Encoding
     * @var string
     */
    protected $_encoding = 'SJIS';
    
    /**
     * Constructor
     *
     * @param $file CSV full file path
     * @param $delimiter CSV delimiter
     * @param $encode CSV encoding
     * @return void
     */
    public function __construct($file, $delimiter = ',', $encode = 'SJIS')
    {
        mb_internal_encoding('UTF-8');
        ini_set('mbstring.language', 'japanese');
        mb_detect_order('EUC-JP,UTF-8,SJIS,JIS,ASCII');
        if ($encode != '') {
            $this->_encoding = $encode;
        }
        parent::__construct($file, $delimiter);
                
        foreach ($this->_header as $key => $value) {
            $this->_header[$key] = mb_convert_encoding($value, 'UTF-8', $this->_encoding);
        }
    }
    
    public function current()
    {
        //$data = $this->_fgetcsv($this->_handle, self::MAX_SIZE, $this->_delimiter);
        $data = fgetcsv($this->_handle, self::MAX_SIZE, $this->_delimiter);

        if (count($data) != 1) {
            while (list($index, $value) = each($data))         {
                $this->_current[$this->_header[$index]] = mb_convert_encoding($value, 'UTF-8', $this->_encoding);
            }
            $this->_row++;
            return $this->_current;
        }
        return array();
    }
}