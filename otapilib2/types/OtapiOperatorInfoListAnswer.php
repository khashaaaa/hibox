<?php

class OtapiOperatorInfoListAnswer extends OtapiAnswer{
    /**
     * @return OtapiDataListOfOperatorInfo
     */
    public function GetResult(){
        $value = isset($this->xmlData->Result) ? $this->xmlData->Result : false;
        return new OtapiDataListOfOperatorInfo($value);
    }
}