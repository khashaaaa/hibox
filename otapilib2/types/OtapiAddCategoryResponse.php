<?php

class OtapiAddCategoryResponse extends BaseOtapiType{
    /**
     * @return OtapiCategoryIdAnswer
     */
    public function GetAddCategoryResult(){
        $value = isset($this->xmlData->AddCategoryResult) ? $this->xmlData->AddCategoryResult : false;
        return new OtapiCategoryIdAnswer($value);
    }
}