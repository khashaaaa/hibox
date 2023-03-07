<?php

class OtapiCityInfoListFrameAnswer extends OtapiAnswer{
    /**
     * @return OtapiDataSubListOfCityInfo
     */
    public function GetResult(){
        $value = isset($this->xmlData->Result) ? $this->xmlData->Result : false;
        return new OtapiDataSubListOfCityInfo($value);
    }
}