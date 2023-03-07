<?php

class OtapiEditCategoryNameByLanguageResponse extends BaseOtapiType{
    /**
     * @return VoidOtapiAnswer
     */
    public function GetEditCategoryNameByLanguageResult(){
        $value = isset($this->xmlData->EditCategoryNameByLanguageResult) ? $this->xmlData->EditCategoryNameByLanguageResult : false;
        return new VoidOtapiAnswer($value);
    }
}