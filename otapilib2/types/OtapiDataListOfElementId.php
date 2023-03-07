<?php

class OtapiDataListOfElementId extends BaseOtapiType{
    /**
     * @return OtapiArrayOfElementId
     */
    public function GetContent(){
        $value = isset($this->xmlData->Content) ? $this->xmlData->Content : false;
        return new OtapiArrayOfElementId($value);
    }
}