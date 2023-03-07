<?php

class OtapiGetCallStatisticsResponse extends BaseOtapiType{
    /**
     * @return OtapiCallStatisticsAnswer
     */
    public function GetGetCallStatisticsResult(){
        $value = isset($this->xmlData->GetCallStatisticsResult) ? $this->xmlData->GetCallStatisticsResult : false;
        return new OtapiCallStatisticsAnswer($value);
    }
}