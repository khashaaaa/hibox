<?php

class OtapiGetSearchCategoryInfoListResponse extends BaseOtapiType{
    /**
     * @return OtapiCategoryListAnswer
     */
    public function GetGetSearchCategoryInfoListResult(){
        $value = isset($this->xmlData->GetSearchCategoryInfoListResult) ? $this->xmlData->GetSearchCategoryInfoListResult : false;
        return new OtapiCategoryListAnswer($value);
    }
}