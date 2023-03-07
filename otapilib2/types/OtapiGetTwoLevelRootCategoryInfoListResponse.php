<?php

class OtapiGetTwoLevelRootCategoryInfoListResponse extends BaseOtapiType{
    /**
     * @return OtapiCategoryListAnswer
     */
    public function GetGetTwoLevelRootCategoryInfoListResult(){
        $value = isset($this->xmlData->GetTwoLevelRootCategoryInfoListResult) ? $this->xmlData->GetTwoLevelRootCategoryInfoListResult : false;
        return new OtapiCategoryListAnswer($value);
    }
}