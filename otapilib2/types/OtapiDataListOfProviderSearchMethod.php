<?php

class OtapiDataListOfProviderSearchMethod extends BaseOtapiType{
    /**
     * @return OtapiArrayOfProviderSearchMethod
     */
    public function GetContent(){
        $value = isset($this->xmlData->Content) ? $this->xmlData->Content : false;
        return new OtapiArrayOfProviderSearchMethod($value);
    }
}