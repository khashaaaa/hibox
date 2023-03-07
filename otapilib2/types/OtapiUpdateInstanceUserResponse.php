<?php

class OtapiUpdateInstanceUserResponse extends BaseOtapiType{
    /**
     * @return VoidOtapiAnswer
     */
    public function GetUpdateInstanceUserResult(){
        $value = isset($this->xmlData->UpdateInstanceUserResult) ? $this->xmlData->UpdateInstanceUserResult : false;
        return new VoidOtapiAnswer($value);
    }
}