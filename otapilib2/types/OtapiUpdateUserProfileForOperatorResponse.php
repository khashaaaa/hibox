<?php

class OtapiUpdateUserProfileForOperatorResponse extends BaseOtapiType{
    /**
     * @return VoidOtapiAnswer
     */
    public function GetUpdateUserProfileForOperatorResult(){
        $value = isset($this->xmlData->UpdateUserProfileForOperatorResult) ? $this->xmlData->UpdateUserProfileForOperatorResult : false;
        return new VoidOtapiAnswer($value);
    }
}