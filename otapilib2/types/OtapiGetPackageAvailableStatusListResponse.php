<?php

class OtapiGetPackageAvailableStatusListResponse extends BaseOtapiType{
    /**
     * @return OtapiPackageStatusInfoListAnswer
     */
    public function GetGetPackageAvailableStatusListResult(){
        $value = isset($this->xmlData->GetPackageAvailableStatusListResult) ? $this->xmlData->GetPackageAvailableStatusListResult : false;
        return new OtapiPackageStatusInfoListAnswer($value);
    }
}