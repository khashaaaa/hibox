<?php

class OtapiUserProfileInfo extends OtapiUserProfileUpdateData{
    /**
     * @return OtapiUserProfileId
     */
    public function GetId(){
        $value = isset($this->xmlData->Id) ? $this->xmlData->Id : false;
        return new OtapiUserProfileId($value);
    }
}