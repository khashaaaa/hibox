<?php

class OtapiInstanceUserInfoListAnswer extends OtapiAnswer{
    /**
     * @return OtapiDataListOfInstanceUserInfo
     */
    public function GetResult(){
        $value = isset($this->xmlData->Result) ? $this->xmlData->Result : false;
        return new OtapiDataListOfInstanceUserInfo($value);
    }
}