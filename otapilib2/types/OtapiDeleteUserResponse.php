<?php

class OtapiDeleteUserResponse extends BaseOtapiType{
    /**
     * @return VoidOtapiAnswer
     */
    public function GetDeleteUserResult(){
        $value = isset($this->xmlData->DeleteUserResult) ? $this->xmlData->DeleteUserResult : false;
        return new VoidOtapiAnswer($value);
    }
}