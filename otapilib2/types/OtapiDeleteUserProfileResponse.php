<?php

class OtapiDeleteUserProfileResponse extends BaseOtapiType{
    /**
     * @return VoidOtapiAnswer
     */
    public function GetDeleteUserProfileResult(){
        $value = isset($this->xmlData->DeleteUserProfileResult) ? $this->xmlData->DeleteUserProfileResult : false;
        return new VoidOtapiAnswer($value);
    }
}