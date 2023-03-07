<?php

class OtapiAddCategoryInfoResponse extends BaseOtapiType{
    /**
     * @return OtapiCategoryAnswer
     */
    public function GetAddCategoryInfoResult(){
        $value = isset($this->xmlData->AddCategoryInfoResult) ? $this->xmlData->AddCategoryInfoResult : false;
        return new OtapiCategoryAnswer($value);
    }
}