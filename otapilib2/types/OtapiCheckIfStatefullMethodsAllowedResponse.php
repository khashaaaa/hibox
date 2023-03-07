<?php

class OtapiCheckIfStatefullMethodsAllowedResponse extends BaseOtapiType{
    /**
     * @return VoidOtapiAnswer
     */
    public function GetCheckIfStatefullMethodsAllowedResult(){
        $value = isset($this->xmlData->CheckIfStatefullMethodsAllowedResult) ? $this->xmlData->CheckIfStatefullMethodsAllowedResult : false;
        return new VoidOtapiAnswer($value);
    }
}