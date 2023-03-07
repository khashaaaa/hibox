<?php

class OtapiProviderInfoAnswer extends OtapiAnswer{
    /**
     * @return OtapiProviderInfo
     */
    public function GetResult(){
        $value = isset($this->xmlData->Result) ? $this->xmlData->Result : false;
        return new OtapiProviderInfo($value);
    }
}