<?php

class OtapiEmailConfirmationInfoAnswer extends OtapiAnswer{
    /**
     * @return OtapiEmailConfirmationInfo
     */
    public function GetEmailConfirmationInfo(){
        $value = isset($this->xmlData->EmailConfirmationInfo) ? $this->xmlData->EmailConfirmationInfo : false;
        return new OtapiEmailConfirmationInfo($value);
    }
}