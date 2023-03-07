<?php

class OtapiCreatePackageResponse extends BaseOtapiType{
    /**
     * @return OtapiPackageAdminInfoAnswer
     */
    public function GetCreatePackageResult(){
        $value = isset($this->xmlData->CreatePackageResult) ? $this->xmlData->CreatePackageResult : false;
        return new OtapiPackageAdminInfoAnswer($value);
    }
}