<?php

class OtapiCallStatisticAnswer extends OtapiAnswer{
    /**
     * @return OtapiCallStatistic
     */
    public function GetResult(){
        $value = isset($this->xmlData->Result) ? $this->xmlData->Result : false;
        return new OtapiCallStatistic($value);
    }
}