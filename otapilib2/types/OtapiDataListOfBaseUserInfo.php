<?php

class OtapiDataListOfBaseUserInfo extends BaseOtapiType{
    /**
     * @return OtapiArrayOfBaseUserInfo
     */
    public function GetContent(){
        $value = isset($this->xmlData->Content) ? $this->xmlData->Content : false;
        return new OtapiArrayOfBaseUserInfo($value);
    }
}