<?php

class OtapiOperatorInfoAnswer extends OtapiAnswer{
    /**
     * @return OtapiOperatorInfo
     */
    public function GetResult(){
        $value = isset($this->xmlData->Result) ? $this->xmlData->Result : false;
        return new OtapiOperatorInfo($value);
    }
}