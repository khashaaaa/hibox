<?php

class OtapiUserProfileInfoListAnswer extends OtapiAnswer{
    /**
     * @return OtapiDataListOfUserProfileInfo
     */
    public function GetResult(){
        $value = isset($this->xmlData->Result) ? $this->xmlData->Result : false;
        return new OtapiDataListOfUserProfileInfo($value);
    }
}