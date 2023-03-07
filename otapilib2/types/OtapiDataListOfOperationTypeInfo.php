<?php

class OtapiDataListOfOperationTypeInfo extends BaseOtapiType{
    /**
     * @return OtapiArrayOfOperationTypeInfo
     */
    public function GetContent(){
        $value = isset($this->xmlData->Content) ? $this->xmlData->Content : false;
        return new OtapiArrayOfOperationTypeInfo($value);
    }
}