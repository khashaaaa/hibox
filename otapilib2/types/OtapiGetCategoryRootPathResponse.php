<?php

class OtapiGetCategoryRootPathResponse extends BaseOtapiType{
    /**
     * @return OtapiCategoryListAnswer
     */
    public function GetGetCategoryRootPathResult(){
        $value = isset($this->xmlData->GetCategoryRootPathResult) ? $this->xmlData->GetCategoryRootPathResult : false;
        return new OtapiCategoryListAnswer($value);
    }
}