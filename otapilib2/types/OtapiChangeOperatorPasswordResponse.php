<?php

class OtapiChangeOperatorPasswordResponse extends BaseOtapiType{
    /**
     * @return VoidOtapiAnswer
     */
    public function GetChangeOperatorPasswordResult(){
        $value = isset($this->xmlData->ChangeOperatorPasswordResult) ? $this->xmlData->ChangeOperatorPasswordResult : false;
        return new VoidOtapiAnswer($value);
    }
}