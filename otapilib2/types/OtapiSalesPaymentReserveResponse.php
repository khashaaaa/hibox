<?php

class OtapiSalesPaymentReserveResponse extends BaseOtapiType{
    /**
     * @return OtapiSalesPaymentInfoAnswer
     */
    public function GetSalesPaymentReserveResult(){
        $value = isset($this->xmlData->SalesPaymentReserveResult) ? $this->xmlData->SalesPaymentReserveResult : false;
        return new OtapiSalesPaymentInfoAnswer($value);
    }
}