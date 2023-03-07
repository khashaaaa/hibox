<?php

class OtapiGetSalesProcessLogResponse extends BaseOtapiType{
    /**
     * @return OtapiSalesProcLogInfoListAnswer
     */
    public function GetGetSalesProcessLogResult(){
        $value = isset($this->xmlData->GetSalesProcessLogResult) ? $this->xmlData->GetSalesProcessLogResult : false;
        return new OtapiSalesProcLogInfoListAnswer($value);
    }
}