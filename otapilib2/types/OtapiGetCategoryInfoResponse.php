<?php

class OtapiGetCategoryInfoResponse extends BaseOtapiType{
    /**
     * @return OtapiCategoryAnswer
     */
    public function GetGetCategoryInfoResult(){
        $value = isset($this->xmlData->GetCategoryInfoResult) ? $this->xmlData->GetCategoryInfoResult : false;
        return new OtapiCategoryAnswer($value);
    }
}