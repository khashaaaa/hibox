<?php

class OtapiEditCategoriesVisibleResponse extends BaseOtapiType{
    /**
     * @return VoidOtapiAnswer
     */
    public function GetEditCategoriesVisibleResult(){
        $value = isset($this->xmlData->EditCategoriesVisibleResult) ? $this->xmlData->EditCategoriesVisibleResult : false;
        return new VoidOtapiAnswer($value);
    }
}