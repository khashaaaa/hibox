<?php

class OtapiGetProviderCategorySubcategoriesResponse extends BaseOtapiType{
    /**
     * @return OtapiCategoryListAnswer
     */
    public function GetGetProviderCategorySubcategoriesResult(){
        $value = isset($this->xmlData->GetProviderCategorySubcategoriesResult) ? $this->xmlData->GetProviderCategorySubcategoriesResult : false;
        return new OtapiCategoryListAnswer($value);
    }
}