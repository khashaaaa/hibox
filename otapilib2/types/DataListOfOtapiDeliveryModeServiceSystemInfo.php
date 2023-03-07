<?php

class DataListOfOtapiDeliveryModeServiceSystemInfo extends BaseOtapiType{
    /**
     * @return ArrayOfOtapiDeliveryModeServiceSystemInfo
     */
    public function GetContent(){
        $value = isset($this->xmlData->Content) ? $this->xmlData->Content : false;
        return new ArrayOfOtapiDeliveryModeServiceSystemInfo($value);
    }
}