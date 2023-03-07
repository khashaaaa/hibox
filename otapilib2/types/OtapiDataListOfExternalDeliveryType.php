<?php

class OtapiDataListOfExternalDeliveryType extends BaseOtapiType{
    /**
     * @return OtapiArrayOfExternalDeliveryType
     */
    public function GetContent(){
        $value = isset($this->xmlData->Content) ? $this->xmlData->Content : false;
        return new OtapiArrayOfExternalDeliveryType($value);
    }
}