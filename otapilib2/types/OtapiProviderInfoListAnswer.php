<?php

class OtapiProviderInfoListAnswer extends OtapiAnswer{
    /**
     * @return OtapiDataListOfProviderInfo
     */
    public function GetResult(){
        $value = isset($this->xmlData->Result) ? $this->xmlData->Result : false;
        return new OtapiDataListOfProviderInfo($value);
    }
}