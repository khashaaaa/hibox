<?php

class OtapiDataListOfNamedProperty extends BaseOtapiType{
    /**
     * @return OtapiArrayOfNamedProperty1
     */
    public function GetContent(){
        $value = isset($this->xmlData->Content) ? $this->xmlData->Content : false;
        return new OtapiArrayOfNamedProperty1($value);
    }
}