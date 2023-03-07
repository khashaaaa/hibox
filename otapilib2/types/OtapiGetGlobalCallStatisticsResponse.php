<?php

class OtapiGetGlobalCallStatisticsResponse extends BaseOtapiType{
    /**
     * @return OtapiCallStatisticsAnswer
     */
    public function GetGetGlobalCallStatisticsResult(){
        $value = isset($this->xmlData->GetGlobalCallStatisticsResult) ? $this->xmlData->GetGlobalCallStatisticsResult : false;
        return new OtapiCallStatisticsAnswer($value);
    }
}