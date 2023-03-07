<?php

class OtapiProviderSearchMethodInfoListAnswer extends OtapiAnswer{
    /**
     * @return OtapiDataListOfProviderSearchMethodInfo
     */
    public function GetResult(){
        $value = isset($this->xmlData->Result) ? $this->xmlData->Result : false;
        return new OtapiDataListOfProviderSearchMethodInfo($value);
    }
}