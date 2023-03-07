<?php

class OtapiInstanceOptionsInfoAnswer extends OtapiAnswer{
    /**
     * @return OtapiInstanceOptionsInfo
     */
    public function GetResult(){
        $value = isset($this->xmlData->Result) ? $this->xmlData->Result : false;
        return new OtapiInstanceOptionsInfo($value);
    }
}