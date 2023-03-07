<?php

class OtapiGetOrderStatusListResponse extends BaseOtapiType{
    /**
     * @return OtapiSalesStatusInfoListAnswer
     */
    public function GetGetOrderStatusListResult(){
        $value = isset($this->xmlData->GetOrderStatusListResult) ? $this->xmlData->GetOrderStatusListResult : false;
        return new OtapiSalesStatusInfoListAnswer($value);
    }
}