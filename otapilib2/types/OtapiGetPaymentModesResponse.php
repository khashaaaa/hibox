<?php

class OtapiGetPaymentModesResponse extends BaseOtapiType{
    /**
     * @return OtapiPaymentModeListAnswer
     */
    public function GetGetPaymentModesResult(){
        $value = isset($this->xmlData->GetPaymentModesResult) ? $this->xmlData->GetPaymentModesResult : false;
        return new OtapiPaymentModeListAnswer($value);
    }
}