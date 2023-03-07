<?php

class OtapiDeliveryModeServiceSystemInfoListAnswer extends OtapiAnswer{
    /**
     * @return DataListOfOtapiDeliveryModeServiceSystemInfo
     */
    public function GetResult(){
        $value = isset($this->xmlData->Result) ? $this->xmlData->Result : false;
        return new DataListOfOtapiDeliveryModeServiceSystemInfo($value);
    }
}