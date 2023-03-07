<?php

class OtapiGetCategoriesOfPriceFormationGroupResponse extends BaseOtapiType{
    /**
     * @return OtapiCategoryListAnswer
     */
    public function GetGetCategoriesOfPriceFormationGroupResult(){
        $value = isset($this->xmlData->GetCategoriesOfPriceFormationGroupResult) ? $this->xmlData->GetCategoriesOfPriceFormationGroupResult : false;
        return new OtapiCategoryListAnswer($value);
    }
}