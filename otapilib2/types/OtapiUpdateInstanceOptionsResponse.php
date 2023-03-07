<?php

class OtapiUpdateInstanceOptionsResponse extends BaseOtapiType{
    /**
     * @return VoidOtapiAnswer
     */
    public function GetUpdateInstanceOptionsResult(){
        $value = isset($this->xmlData->UpdateInstanceOptionsResult) ? $this->xmlData->UpdateInstanceOptionsResult : false;
        return new VoidOtapiAnswer($value);
    }
}