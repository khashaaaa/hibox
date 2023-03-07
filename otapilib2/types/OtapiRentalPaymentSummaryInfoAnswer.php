<?php

class OtapiRentalPaymentSummaryInfoAnswer extends OtapiAnswer{
    /**
     * @return OtapiRentalPaymentSummaryInfo
     */
    public function GetResult(){
        $value = isset($this->xmlData->Result) ? $this->xmlData->Result : false;
        return new OtapiRentalPaymentSummaryInfo($value);
    }
}