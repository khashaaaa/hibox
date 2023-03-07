<?php

class OtapiGetPackageResponse extends BaseOtapiType{
    /**
     * @return OtapiPackageAdminInfoAnswer
     */
    public function GetGetPackageResult(){
        $value = isset($this->xmlData->GetPackageResult) ? $this->xmlData->GetPackageResult : false;
        return new OtapiPackageAdminInfoAnswer($value);
    }
}