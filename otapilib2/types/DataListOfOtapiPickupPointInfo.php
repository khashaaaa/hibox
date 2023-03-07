<?php

class DataListOfOtapiPickupPointInfo extends BaseOtapiType{
    /**
     * @return ArrayOfOtapiPickupPointInfo
     */
    public function GetContent(){
        $value = isset($this->xmlData->Content) ? $this->xmlData->Content : false;
        return new ArrayOfOtapiPickupPointInfo($value);
    }
}