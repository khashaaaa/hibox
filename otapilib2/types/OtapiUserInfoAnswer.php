<?php

class OtapiUserInfoAnswer extends OtapiAnswer{
    /**
     * @return OtapiUserInfo
     */
    public function GetUserInfo(){
        $value = isset($this->xmlData->UserInfo) ? $this->xmlData->UserInfo : false;
        return new OtapiUserInfo($value);
    }
}