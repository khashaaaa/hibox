<?php

class OtapiStrategyInfoAnswer extends OtapiAnswer{
    /**
     * @return OtapiStrategyInfo
     */
    public function GetResult(){
        $value = isset($this->xmlData->Result) ? $this->xmlData->Result : false;
        return new OtapiStrategyInfo($value);
    }
}