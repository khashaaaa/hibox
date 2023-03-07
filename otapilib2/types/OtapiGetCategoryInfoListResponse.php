<?php

class OtapiGetCategoryInfoListResponse extends BaseOtapiType{
    /**
     * @return OtapiCategoryListAnswer
     */
    public function GetGetCategoryInfoListResult(){
        $value = isset($this->xmlData->GetCategoryInfoListResult) ? $this->xmlData->GetCategoryInfoListResult : false;
        return new OtapiCategoryListAnswer($value);
    }
}