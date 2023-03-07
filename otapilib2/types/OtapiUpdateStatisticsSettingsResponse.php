<?php

class OtapiUpdateStatisticsSettingsResponse extends BaseOtapiType{
    /**
     * @return VoidOtapiAnswer
     */
    public function GetUpdateStatisticsSettingsResult(){
        $value = isset($this->xmlData->UpdateStatisticsSettingsResult) ? $this->xmlData->UpdateStatisticsSettingsResult : false;
        return new VoidOtapiAnswer($value);
    }
}