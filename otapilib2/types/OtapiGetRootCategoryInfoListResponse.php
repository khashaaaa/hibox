<?php

class OtapiGetRootCategoryInfoListResponse extends BaseOtapiType{
    /**
     * @return OtapiCategoryListAnswer
     */
    public function GetGetRootCategoryInfoListResult(){
        $value = isset($this->xmlData->GetRootCategoryInfoListResult) ? $this->xmlData->GetRootCategoryInfoListResult : false;
        return new OtapiCategoryListAnswer($value);
    }
}