<?php

class OtapiPackageAdminInfoAnswer extends OtapiAnswer{
    /**
     * @return OtapiPackageAdminInfo
     */
    public function GetResult(){
        $value = isset($this->xmlData->Result) ? $this->xmlData->Result : false;
        return new OtapiPackageAdminInfo($value);
    }
}