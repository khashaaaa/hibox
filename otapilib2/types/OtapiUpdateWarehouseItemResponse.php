<?php

class OtapiUpdateWarehouseItemResponse extends BaseOtapiType{
    /**
     * @return VoidOtapiAnswer
     */
    public function GetUpdateWarehouseItemResult(){
        $value = isset($this->xmlData->UpdateWarehouseItemResult) ? $this->xmlData->UpdateWarehouseItemResult : false;
        return new VoidOtapiAnswer($value);
    }
}