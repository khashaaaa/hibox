<?php

class OtapiEditCategoryExternalIdResponse extends BaseOtapiType{
    /**
     * @return VoidOtapiAnswer
     */
    public function GetEditCategoryExternalIdResult(){
        $value = isset($this->xmlData->EditCategoryExternalIdResult) ? $this->xmlData->EditCategoryExternalIdResult : false;
        return new VoidOtapiAnswer($value);
    }
}