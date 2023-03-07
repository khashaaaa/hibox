<?php

class OtapiDataListOfTextByLang extends BaseOtapiType{
    /**
     * @return OtapiArrayOfTextByLang
     */
    public function GetContent(){
        $value = isset($this->xmlData->Content) ? $this->xmlData->Content : false;
        return new OtapiArrayOfTextByLang($value);
    }
}