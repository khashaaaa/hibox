<?php

class OtapiEditOrderOfCategoryResponse extends BaseOtapiType{
    /**
     * @return VoidOtapiAnswer
     */
    public function GetEditOrderOfCategoryResult(){
        $value = isset($this->xmlData->EditOrderOfCategoryResult) ? $this->xmlData->EditOrderOfCategoryResult : false;
        return new VoidOtapiAnswer($value);
    }
}