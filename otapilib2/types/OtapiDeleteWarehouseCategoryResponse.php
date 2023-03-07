<?php

class OtapiDeleteWarehouseCategoryResponse extends BaseOtapiType{
    /**
     * @return VoidOtapiAnswer
     */
    public function GetDeleteWarehouseCategoryResult(){
        $value = isset($this->xmlData->DeleteWarehouseCategoryResult) ? $this->xmlData->DeleteWarehouseCategoryResult : false;
        return new VoidOtapiAnswer($value);
    }
}