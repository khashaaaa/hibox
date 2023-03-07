<?php

class OtapiCommonInstanceOptionsInfoAnswer extends OtapiAnswer{
    /**
     * @return OtapiCommonInstanceOptionsInfo
     */
    public function GetResult(){
        $value = isset($this->xmlData->Result) ? $this->xmlData->Result : false;
        return new OtapiCommonInstanceOptionsInfo($value);
    }
}