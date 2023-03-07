<?php

class OtapiItemFullInfoAnswer extends OtapiAnswer{
    /**
     * @return OtapiItemFullInfo
     */
    public function GetOtapiItemFullInfo(){
        $value = isset($this->xmlData->OtapiItemFullInfo) ? $this->xmlData->OtapiItemFullInfo : false;
        return new OtapiItemFullInfo($value);
    }
}