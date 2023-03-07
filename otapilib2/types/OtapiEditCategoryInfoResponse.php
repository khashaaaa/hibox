<?php

class OtapiEditCategoryInfoResponse extends BaseOtapiType{
    /**
     * @return OtapiCategoryAnswer
     */
    public function GetEditCategoryInfoResult(){
        $value = isset($this->xmlData->EditCategoryInfoResult) ? $this->xmlData->EditCategoryInfoResult : false;
        return new OtapiCategoryAnswer($value);
    }
}