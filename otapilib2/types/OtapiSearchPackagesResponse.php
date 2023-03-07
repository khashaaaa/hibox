<?php

class OtapiSearchPackagesResponse extends BaseOtapiType{
    /**
     * @return OtapiPackageAdminInfoListFrameAnswer
     */
    public function GetSearchPackagesResult(){
        $value = isset($this->xmlData->SearchPackagesResult) ? $this->xmlData->SearchPackagesResult : false;
        return new OtapiPackageAdminInfoListFrameAnswer($value);
    }
}