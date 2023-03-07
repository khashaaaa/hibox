<?php

class OtapiGetCustomerSalesProcessLogResponse extends BaseOtapiType{
    /**
     * @return OtapiSalesProcLogInfoListAnswer
     */
    public function GetGetCustomerSalesProcessLogResult(){
        $value = isset($this->xmlData->GetCustomerSalesProcessLogResult) ? $this->xmlData->GetCustomerSalesProcessLogResult : false;
        return new OtapiSalesProcLogInfoListAnswer($value);
    }
}