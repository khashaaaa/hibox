<?php

class OtapiUpdatePackageResponse extends BaseOtapiType{
    /**
     * @return OtapiPackageAdminInfoAnswer
     */
    public function GetUpdatePackageResult(){
        $value = isset($this->xmlData->UpdatePackageResult) ? $this->xmlData->UpdatePackageResult : false;
        return new OtapiPackageAdminInfoAnswer($value);
    }
}