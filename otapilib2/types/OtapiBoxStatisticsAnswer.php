<?php

class OtapiBoxStatisticsAnswer extends OtapiAnswer{
    /**
     * @return OtapiBoxStatistics
     */
    public function GetResult(){
        $value = isset($this->xmlData->Result) ? $this->xmlData->Result : false;
        return new OtapiBoxStatistics($value);
    }
}