<?php

class OtapiRemoveExternalDeliveryRateResponse extends BaseOtapiType{
    /**
     * @return VoidOtapiAnswer
     */
    public function GetRemoveExternalDeliveryRateResult(){
        $value = isset($this->xmlData->RemoveExternalDeliveryRateResult) ? $this->xmlData->RemoveExternalDeliveryRateResult : false;
        return new VoidOtapiAnswer($value);
    }
}