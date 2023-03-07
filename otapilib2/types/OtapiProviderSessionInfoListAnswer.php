<?php

class OtapiProviderSessionInfoListAnswer extends OtapiAnswer{
    /**
     * @return OtapiDataListOfProviderSessionInfo
     */
    public function GetResult(){
        $value = isset($this->xmlData->Result) ? $this->xmlData->Result : false;
        return new OtapiDataListOfProviderSessionInfo($value);
    }
}