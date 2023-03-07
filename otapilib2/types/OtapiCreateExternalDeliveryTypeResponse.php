<?php

class OtapiCreateExternalDeliveryTypeResponse extends BaseOtapiType{
    /**
     * @return OtapiCreateExternalDeliveryTypeAnswer
     */
    public function GetCreateExternalDeliveryTypeResult(){
        $value = isset($this->xmlData->CreateExternalDeliveryTypeResult) ? $this->xmlData->CreateExternalDeliveryTypeResult : false;
        return new OtapiCreateExternalDeliveryTypeAnswer($value);
    }
}