<?php

class OtapiEditExternalDeliveryRateResponse extends BaseOtapiType{
    /**
     * @return VoidOtapiAnswer
     */
    public function GetEditExternalDeliveryRateResult(){
        $value = isset($this->xmlData->EditExternalDeliveryRateResult) ? $this->xmlData->EditExternalDeliveryRateResult : false;
        return new VoidOtapiAnswer($value);
    }
}