<?php

class OtapiUpdateUserPreferencesResponse extends BaseOtapiType{
    /**
     * @return VoidOtapiAnswer
     */
    public function GetUpdateUserPreferencesResult(){
        $value = isset($this->xmlData->UpdateUserPreferencesResult) ? $this->xmlData->UpdateUserPreferencesResult : false;
        return new VoidOtapiAnswer($value);
    }
}