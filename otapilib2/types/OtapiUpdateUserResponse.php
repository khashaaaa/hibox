<?php

class OtapiUpdateUserResponse extends BaseOtapiType{
    /**
     * @return VoidOtapiAnswer
     */
    public function GetUpdateUserResult(){
        $value = isset($this->xmlData->UpdateUserResult) ? $this->xmlData->UpdateUserResult : false;
        return new VoidOtapiAnswer($value);
    }
}