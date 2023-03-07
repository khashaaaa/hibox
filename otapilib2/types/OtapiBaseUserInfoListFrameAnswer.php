<?php

class OtapiBaseUserInfoListFrameAnswer extends OtapiAnswer{
    /**
     * @return OtapiDataSubListOfBaseUserInfo
     */
    public function GetResult(){
        $value = isset($this->xmlData->Result) ? $this->xmlData->Result : false;
        return new OtapiDataSubListOfBaseUserInfo($value);
    }
}