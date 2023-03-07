<?php

class OtapiUpdateWarehouseCategoryResponse extends BaseOtapiType{
    /**
     * @return VoidOtapiAnswer
     */
    public function GetUpdateWarehouseCategoryResult(){
        $value = isset($this->xmlData->UpdateWarehouseCategoryResult) ? $this->xmlData->UpdateWarehouseCategoryResult : false;
        return new VoidOtapiAnswer($value);
    }
}