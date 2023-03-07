<?php

class OtapiAuthenticationInfoAnswer extends OtapiAnswer{
    /**
     * @return OtapiAuthenticationInfo
     */
    public function GetResult(){
        $value = isset($this->xmlData->Result) ? $this->xmlData->Result : false;
        return new OtapiAuthenticationInfo($value);
    }
}