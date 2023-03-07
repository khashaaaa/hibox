<?php

class OtapiDeleteUserProfileForOperatorResponse extends BaseOtapiType{
    /**
     * @return VoidOtapiAnswer
     */
    public function GetDeleteUserProfileForOperatorResult(){
        $value = isset($this->xmlData->DeleteUserProfileForOperatorResult) ? $this->xmlData->DeleteUserProfileForOperatorResult : false;
        return new VoidOtapiAnswer($value);
    }
}