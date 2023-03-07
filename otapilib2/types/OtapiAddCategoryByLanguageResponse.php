<?php

class OtapiAddCategoryByLanguageResponse extends BaseOtapiType{
    /**
     * @return OtapiCategoryIdAnswer
     */
    public function GetAddCategoryByLanguageResult(){
        $value = isset($this->xmlData->AddCategoryByLanguageResult) ? $this->xmlData->AddCategoryByLanguageResult : false;
        return new OtapiCategoryIdAnswer($value);
    }
}