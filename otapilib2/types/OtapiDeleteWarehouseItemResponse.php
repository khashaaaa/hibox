<?php

class OtapiDeleteWarehouseItemResponse extends BaseOtapiType{
    /**
     * @return VoidOtapiAnswer
     */
    public function GetDeleteWarehouseItemResult(){
        $value = isset($this->xmlData->DeleteWarehouseItemResult) ? $this->xmlData->DeleteWarehouseItemResult : false;
        return new VoidOtapiAnswer($value);
    }
}