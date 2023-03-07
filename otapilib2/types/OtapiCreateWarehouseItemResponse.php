<?php

class OtapiCreateWarehouseItemResponse extends BaseOtapiType{
    /**
     * @return OtapiCreateWarehouseItemAnswer
     */
    public function GetCreateWarehouseItemResult(){
        $value = isset($this->xmlData->CreateWarehouseItemResult) ? $this->xmlData->CreateWarehouseItemResult : false;
        return new OtapiCreateWarehouseItemAnswer($value);
    }
}