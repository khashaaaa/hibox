<?php

class OtapiPickupPointInfoListFrameAnswer extends OtapiAnswer{
    /**
     * @return DataSubListOfOtapiPickupPointInfo
     */
    public function GetResult(){
        $value = isset($this->xmlData->Result) ? $this->xmlData->Result : false;
        return new DataSubListOfOtapiPickupPointInfo($value);
    }
}