<?php

class OtapiUserStatusData extends BaseOtapiType{
    /**
     * @return boolean
     */
    public function IsSessionExpired(){
        $value = isset($this->xmlData->IsSessionExpired) ? (string)$this->xmlData->IsSessionExpired : false;
        $propertyType = 'boolean';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return OtapiUserStatusInfo
     */
    public function GetInfo(){
        $value = isset($this->xmlData->Info) ? $this->xmlData->Info : false;
        return new OtapiUserStatusInfo($value);
    }
}