<?php

class OtapiGetDeliveryModesWithPriceResponse extends BaseOtapiType{
    /**
     * @return OtapiDeliveryModeListAnswer
     */
    public function GetGetDeliveryModesWithPriceResult(){
        $value = isset($this->xmlData->GetDeliveryModesWithPriceResult) ? $this->xmlData->GetDeliveryModesWithPriceResult : false;
        return new OtapiDeliveryModeListAnswer($value);
    }
}