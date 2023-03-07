<?php

class OtapiGetUserPreferencesResponse extends BaseOtapiType{
    /**
     * @return OtapiUserPreferencesAnswer
     */
    public function GetGetUserPreferencesResult(){
        $value = isset($this->xmlData->GetUserPreferencesResult) ? $this->xmlData->GetUserPreferencesResult : false;
        return new OtapiUserPreferencesAnswer($value);
    }
}