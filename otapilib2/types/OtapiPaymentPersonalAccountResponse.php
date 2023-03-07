<?php

class OtapiPaymentPersonalAccountResponse extends BaseOtapiType{
    /**
     * @return VoidOtapiAnswer
     */
    public function GetPaymentPersonalAccountResult(){
        $value = isset($this->xmlData->PaymentPersonalAccountResult) ? $this->xmlData->PaymentPersonalAccountResult : false;
        return new VoidOtapiAnswer($value);
    }
}