<?php

class OtapiEditCategoryNameResponse extends BaseOtapiType{
    /**
     * @return VoidOtapiAnswer
     */
    public function GetEditCategoryNameResult(){
        $value = isset($this->xmlData->EditCategoryNameResult) ? $this->xmlData->EditCategoryNameResult : false;
        return new VoidOtapiAnswer($value);
    }
}