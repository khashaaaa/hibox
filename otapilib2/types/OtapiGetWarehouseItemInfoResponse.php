<?php

class OtapiGetWarehouseItemInfoResponse extends BaseOtapiType{
    /**
     * @return OtapiWarehouseItemInfoAnswer
     */
    public function GetGetWarehouseItemInfoResult(){
        $value = isset($this->xmlData->GetWarehouseItemInfoResult) ? $this->xmlData->GetWarehouseItemInfoResult : false;
        return new OtapiWarehouseItemInfoAnswer($value);
    }
}