<?php

class OtapiGetWarehouseCategoryInfoResponse extends BaseOtapiType{
    /**
     * @return OtapiWarehouseCategoryInfoAnswer
     */
    public function GetGetWarehouseCategoryInfoResult(){
        $value = isset($this->xmlData->GetWarehouseCategoryInfoResult) ? $this->xmlData->GetWarehouseCategoryInfoResult : false;
        return new OtapiWarehouseCategoryInfoAnswer($value);
    }
}