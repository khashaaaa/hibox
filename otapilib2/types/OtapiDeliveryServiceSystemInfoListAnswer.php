<?php

class OtapiDeliveryServiceSystemInfoListAnswer extends OtapiAnswer{
    /**
     * @return DataListOfOtapiDeliveryServiceSystemInfo
     */
    public function GetResult(){
        $value = isset($this->xmlData->Result) ? $this->xmlData->Result : false;
        return new DataListOfOtapiDeliveryServiceSystemInfo($value);
    }
}