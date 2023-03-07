<?php

class OtapiEditUserResponse extends BaseOtapiType{
    /**
     * @return VoidOtapiAnswer
     */
    public function GetEditUserResult(){
        $value = isset($this->xmlData->EditUserResult) ? $this->xmlData->EditUserResult : false;
        return new VoidOtapiAnswer($value);
    }
}