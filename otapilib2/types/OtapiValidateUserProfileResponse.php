<?php

class OtapiValidateUserProfileResponse extends BaseOtapiType{
    /**
     * @return VoidOtapiAnswer
     */
    public function GetValidateUserProfileResult(){
        $value = isset($this->xmlData->ValidateUserProfileResult) ? $this->xmlData->ValidateUserProfileResult : false;
        return new VoidOtapiAnswer($value);
    }
}