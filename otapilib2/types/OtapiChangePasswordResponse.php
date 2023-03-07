<?php

class OtapiChangePasswordResponse extends BaseOtapiType{
    /**
     * @return VoidOtapiAnswer
     */
    public function GetChangePasswordResult(){
        $value = isset($this->xmlData->ChangePasswordResult) ? $this->xmlData->ChangePasswordResult : false;
        return new VoidOtapiAnswer($value);
    }
}