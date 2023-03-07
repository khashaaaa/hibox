<?php

class OtapiSalesPaymentInfoAnswer extends OtapiAnswer{
    /**
     * @return OtapiSalesPaymentInfo
     */
    public function GetResult(){
        $value = isset($this->xmlData->Result) ? $this->xmlData->Result : false;
        return new OtapiSalesPaymentInfo($value);
    }
}