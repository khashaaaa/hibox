<?php

class OtapiDataListOfExternalDeliveryRate extends BaseOtapiType{
    /**
     * @return OtapiArrayOfExternalDeliveryRate
     */
    public function GetContent(){
        $value = isset($this->xmlData->Content) ? $this->xmlData->Content : false;
        return new OtapiArrayOfExternalDeliveryRate($value);
    }
}