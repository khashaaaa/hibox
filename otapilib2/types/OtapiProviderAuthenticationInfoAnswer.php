<?php

class OtapiProviderAuthenticationInfoAnswer extends OtapiAnswer{
    /**
     * @return OtapiProviderAuthenticationInfo
     */
    public function GetResult(){
        $value = isset($this->xmlData->Result) ? $this->xmlData->Result : false;
        return new OtapiProviderAuthenticationInfo($value);
    }
}