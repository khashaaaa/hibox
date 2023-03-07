<?php

class OtapiBoxStatisticsListAnswer extends OtapiAnswer{
    /**
     * @return DataListOfOtapiBoxStatistics
     */
    public function GetResult(){
        $value = isset($this->xmlData->Result) ? $this->xmlData->Result : false;
        return new DataListOfOtapiBoxStatistics($value);
    }
}