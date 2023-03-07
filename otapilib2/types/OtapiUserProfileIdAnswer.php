<?php

class OtapiUserProfileIdAnswer extends OtapiAnswer{
    /**
     * @return OtapiUserProfileId
     */
    public function GetResult(){
        $value = isset($this->xmlData->Result) ? $this->xmlData->Result : false;
        return new OtapiUserProfileId($value);
    }
}