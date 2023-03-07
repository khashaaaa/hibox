<?php

class OtapiProviderSearchMethodListAnswer extends OtapiAnswer{
    /**
     * @return OtapiDataListOfProviderSearchMethod
     */
    public function GetResult(){
        $value = isset($this->xmlData->Result) ? $this->xmlData->Result : false;
        return new OtapiDataListOfProviderSearchMethod($value);
    }
}