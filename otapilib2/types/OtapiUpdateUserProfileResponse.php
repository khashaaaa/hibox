<?php

class OtapiUpdateUserProfileResponse extends BaseOtapiType{
    /**
     * @return VoidOtapiAnswer
     */
    public function GetUpdateUserProfileResult(){
        $value = isset($this->xmlData->UpdateUserProfileResult) ? $this->xmlData->UpdateUserProfileResult : false;
        return new VoidOtapiAnswer($value);
    }
}