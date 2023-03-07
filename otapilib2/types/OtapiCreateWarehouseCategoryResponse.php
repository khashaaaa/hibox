<?php

class OtapiCreateWarehouseCategoryResponse extends BaseOtapiType{
    /**
     * @return OtapiCreateWarehouseCategoryAnswer
     */
    public function GetCreateWarehouseCategoryResult(){
        $value = isset($this->xmlData->CreateWarehouseCategoryResult) ? $this->xmlData->CreateWarehouseCategoryResult : false;
        return new OtapiCreateWarehouseCategoryAnswer($value);
    }
}