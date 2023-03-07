<?php

class OtapiGetProviderCategoryRootPathResponse extends BaseOtapiType{
    /**
     * @return OtapiCategoryListAnswer
     */
    public function GetGetProviderCategoryRootPathResult(){
        $value = isset($this->xmlData->GetProviderCategoryRootPathResult) ? $this->xmlData->GetProviderCategoryRootPathResult : false;
        return new OtapiCategoryListAnswer($value);
    }
}