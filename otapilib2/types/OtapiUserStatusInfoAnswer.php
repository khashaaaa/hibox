<?php

class OtapiUserStatusInfoAnswer extends OtapiAnswer{
    /**
     * @return OtapiUserStatusInfo
     */
    public function GetUserStatusInfo(){
        $value = isset($this->xmlData->UserStatusInfo) ? $this->xmlData->UserStatusInfo : false;
        return new OtapiUserStatusInfo($value);
    }
}