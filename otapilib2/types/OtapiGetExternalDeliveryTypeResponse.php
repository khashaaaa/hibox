<?php

class OtapiGetExternalDeliveryTypeResponse extends BaseOtapiType{
    /**
     * @return OtapiExternalDeliveryTypeAnswer
     */
    public function GetGetExternalDeliveryTypeResult(){
        $value = isset($this->xmlData->GetExternalDeliveryTypeResult) ? $this->xmlData->GetExternalDeliveryTypeResult : false;
        return new OtapiExternalDeliveryTypeAnswer($value);
    }
}