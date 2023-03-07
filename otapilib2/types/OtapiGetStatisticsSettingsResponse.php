<?php

class OtapiGetStatisticsSettingsResponse extends BaseOtapiType{
    /**
     * @return OtapiStatisticsSettingsInfoAnswer
     */
    public function GetGetStatisticsSettingsResult(){
        $value = isset($this->xmlData->GetStatisticsSettingsResult) ? $this->xmlData->GetStatisticsSettingsResult : false;
        return new OtapiStatisticsSettingsInfoAnswer($value);
    }
}