<?php

class OtapiGetCategorySubcategoryInfoListResponse extends BaseOtapiType{
    /**
     * @return OtapiCategoryListAnswer
     */
    public function GetGetCategorySubcategoryInfoListResult(){
        $value = isset($this->xmlData->GetCategorySubcategoryInfoListResult) ? $this->xmlData->GetCategorySubcategoryInfoListResult : false;
        return new OtapiCategoryListAnswer($value);
    }
}