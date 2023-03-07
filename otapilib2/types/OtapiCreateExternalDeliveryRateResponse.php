<?php

class OtapiCreateExternalDeliveryRateResponse extends BaseOtapiType{
    /**
     * @return OtapiCreateExternalDeliveryRateAnswer
     */
    public function GetCreateExternalDeliveryRateResult(){
        $value = isset($this->xmlData->CreateExternalDeliveryRateResult) ? $this->xmlData->CreateExternalDeliveryRateResult : false;
        return new OtapiCreateExternalDeliveryRateAnswer($value);
    }
}