<?php

class OtapiStrategyInfoListAnswer extends OtapiAnswer{
    /**
     * @return OtapiDataListOfStrategyInfo
     */
    public function GetResult(){
        $value = isset($this->xmlData->Result) ? $this->xmlData->Result : false;
        return new OtapiDataListOfStrategyInfo($value);
    }
}