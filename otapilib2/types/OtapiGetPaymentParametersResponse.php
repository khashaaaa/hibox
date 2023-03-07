<?php

class OtapiGetPaymentParametersResponse extends BaseOtapiType{
    /**
     * @return OtapiPaymentFormAnswer
     */
    public function GetGetPaymentParametersResult(){
        $value = isset($this->xmlData->GetPaymentParametersResult) ? $this->xmlData->GetPaymentParametersResult : false;
        return new OtapiPaymentFormAnswer($value);
    }
}