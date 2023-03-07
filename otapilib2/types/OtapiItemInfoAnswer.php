<?php

class OtapiItemInfoAnswer extends OtapiAnswer{
    /**
     * @return OtapiItemInfo
     */
    public function GetOtapiItemInfo(){
        $value = isset($this->xmlData->OtapiItemInfo) ? $this->xmlData->OtapiItemInfo : false;
        return new OtapiItemInfo($value);
    }
}