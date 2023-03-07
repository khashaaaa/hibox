<?php

class OtapiAuthenticationSystemInfoListAnswer extends OtapiAnswer{
    /**
     * @return OtapiDataListOfAuthenticationSystemInfo
     */
    public function GetResult(){
        $value = isset($this->xmlData->Result) ? $this->xmlData->Result : false;
        return new OtapiDataListOfAuthenticationSystemInfo($value);
    }
}