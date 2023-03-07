<?php

class OtapiGetExternalDeliveryRateResponse extends BaseOtapiType{
    /**
     * @return OtapiExternalDeliveryRateAnswer
     */
    public function GetGetExternalDeliveryRateResult(){
        $value = isset($this->xmlData->GetExternalDeliveryRateResult) ? $this->xmlData->GetExternalDeliveryRateResult : false;
        return new OtapiExternalDeliveryRateAnswer($value);
    }
}