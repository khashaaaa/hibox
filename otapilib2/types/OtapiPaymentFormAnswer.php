<?php

class OtapiPaymentFormAnswer extends OtapiAnswer{
    /**
     * @return OtapiPaymentForm
     */
    public function GetResult(){
        $value = isset($this->xmlData->Result) ? $this->xmlData->Result : false;
        return new OtapiPaymentForm($value);
    }
}