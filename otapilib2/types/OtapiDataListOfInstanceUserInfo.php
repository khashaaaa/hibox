<?php

class OtapiDataListOfInstanceUserInfo extends BaseOtapiType{
    /**
     * @return OtapiArrayOfInstanceUserInfo
     */
    public function GetContent(){
        $value = isset($this->xmlData->Content) ? $this->xmlData->Content : false;
        return new OtapiArrayOfInstanceUserInfo($value);
    }
}