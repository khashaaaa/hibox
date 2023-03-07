<?php

class OtapiCallStatisticsAnswer extends OtapiAnswer{
    /**
     * @return OtapiCallStatistics
     */
    public function GetResult(){
        $value = isset($this->xmlData->Result) ? $this->xmlData->Result : false;
        return new OtapiCallStatistics($value);
    }
}