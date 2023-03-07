<?php

class OtapiSynchronizationMode {
    /**
     * @var \SimpleXMLElement
     */
    private $xmlData;

    public function __construct($xmlData){
        $this->xmlData = $xmlData;
    }

    public function GetValue(){
        $restrictionType = 'string';
        return $restrictionType == 'boolean' ? (string)$this->xmlData == 'true' : (string)$this->xmlData;
    }


}