<?php

class OtapiGetSaleLineProcessLogResponse extends BaseOtapiType{
    /**
     * @return OtapiSalesLineProcLogInfoListAnswer
     */
    public function GetGetSaleLineProcessLogResult(){
        $value = isset($this->xmlData->GetSaleLineProcessLogResult) ? $this->xmlData->GetSaleLineProcessLogResult : false;
        return new OtapiSalesLineProcLogInfoListAnswer($value);
    }
}