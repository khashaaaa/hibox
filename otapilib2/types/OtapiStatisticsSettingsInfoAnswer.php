<?php

class OtapiStatisticsSettingsInfoAnswer extends OtapiAnswer{
    /**
     * @return OtapiStatisticsSettingsInfo
     */
    public function GetResult(){
        $value = isset($this->xmlData->Result) ? $this->xmlData->Result : false;
        return new OtapiStatisticsSettingsInfo($value);
    }
}