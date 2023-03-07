<?php

class DataListOfOtapiDeliveryServiceSystemInfo extends BaseOtapiType{
    /**
     * @return ArrayOfOtapiDeliveryServiceSystemInfo
     */
    public function GetContent(){
        $value = isset($this->xmlData->Content) ? $this->xmlData->Content : false;
        return new ArrayOfOtapiDeliveryServiceSystemInfo($value);
    }
}