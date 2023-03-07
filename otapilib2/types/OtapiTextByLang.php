<?php

class OtapiTextByLang extends BaseOtapiType{
    /**
     * @return string
     */
    public function GetText(){
        $value = isset($this->xmlData->Text) ? (string)$this->xmlData->Text : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetLang(){
        $value = isset($this->xmlData->Lang) ? (string)$this->xmlData->Lang : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
}