<?php

class OtapiGetSalesPaymentInfoResponse extends BaseOtapiType{
    /**
     * @return OtapiSalesPaymentInfoAnswer
     */
    public function GetGetSalesPaymentInfoResult(){
        $value = isset($this->xmlData->GetSalesPaymentInfoResult) ? $this->xmlData->GetSalesPaymentInfoResult : false;
        return new OtapiSalesPaymentInfoAnswer($value);
    }
}