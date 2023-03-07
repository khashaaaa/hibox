<?php

class OtapiCalculateRentResponse extends BaseOtapiType{
    /**
     * @return OtapiRentalPaymentSummaryInfoAnswer
     */
    public function GetCalculateRentResult(){
        $value = isset($this->xmlData->CalculateRentResult) ? $this->xmlData->CalculateRentResult : false;
        return new OtapiRentalPaymentSummaryInfoAnswer($value);
    }
}