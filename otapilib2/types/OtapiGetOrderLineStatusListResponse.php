<?php

class OtapiGetOrderLineStatusListResponse extends BaseOtapiType{
    /**
     * @return OtapiSalesLineStatusInfoListAnswer
     */
    public function GetGetOrderLineStatusListResult(){
        $value = isset($this->xmlData->GetOrderLineStatusListResult) ? $this->xmlData->GetOrderLineStatusListResult : false;
        return new OtapiSalesLineStatusInfoListAnswer($value);
    }
}