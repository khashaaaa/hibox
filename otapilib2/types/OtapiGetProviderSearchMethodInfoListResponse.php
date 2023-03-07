<?php

class OtapiGetProviderSearchMethodInfoListResponse extends BaseOtapiType{
    /**
     * @return OtapiProviderSearchMethodInfoListAnswer
     */
    public function GetGetProviderSearchMethodInfoListResult(){
        $value = isset($this->xmlData->GetProviderSearchMethodInfoListResult) ? $this->xmlData->GetProviderSearchMethodInfoListResult : false;
        return new OtapiProviderSearchMethodInfoListAnswer($value);
    }
}