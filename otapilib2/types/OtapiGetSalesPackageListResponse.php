<?php

class OtapiGetSalesPackageListResponse extends BaseOtapiType{
    /**
     * @return OtapiPackageAdminInfoListAnswer
     */
    public function GetGetSalesPackageListResult(){
        $value = isset($this->xmlData->GetSalesPackageListResult) ? $this->xmlData->GetSalesPackageListResult : false;
        return new OtapiPackageAdminInfoListAnswer($value);
    }
}