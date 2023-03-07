<?php

class OtapiGetAvailableProviderSearchMethodInfoListForSearchParametersResponse extends BaseOtapiType{
    /**
     * @return OtapiProviderSearchMethodInfoListAnswer
     */
    public function GetGetAvailableProviderSearchMethodInfoListForSearchParametersResult(){
        $value = isset($this->xmlData->GetAvailableProviderSearchMethodInfoListForSearchParametersResult) ? $this->xmlData->GetAvailableProviderSearchMethodInfoListForSearchParametersResult : false;
        return new OtapiProviderSearchMethodInfoListAnswer($value);
    }
}