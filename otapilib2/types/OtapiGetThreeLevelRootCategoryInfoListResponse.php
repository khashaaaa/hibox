<?php

class OtapiGetThreeLevelRootCategoryInfoListResponse extends BaseOtapiType{
    /**
     * @return OtapiCategoryListAnswer
     */
    public function GetGetThreeLevelRootCategoryInfoListResult(){
        $value = isset($this->xmlData->GetThreeLevelRootCategoryInfoListResult) ? $this->xmlData->GetThreeLevelRootCategoryInfoListResult : false;
        return new OtapiCategoryListAnswer($value);
    }
}