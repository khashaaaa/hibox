<?php

class OtapiConfirmationInfoAnswer extends OtapiAnswer{
    /**
     * @return OtapiConfirmationInfo
     */
    public function GetEmailConfirmationInfo(){
        $value = isset($this->xmlData->EmailConfirmationInfo) ? $this->xmlData->EmailConfirmationInfo : false;
        return new OtapiConfirmationInfo($value);
    }
}