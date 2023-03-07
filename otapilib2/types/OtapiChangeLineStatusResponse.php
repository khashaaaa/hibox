<?php

class OtapiChangeLineStatusResponse extends BaseOtapiType{
    /**
     * @return VoidOtapiAnswer
     */
    public function GetChangeLineStatusResult(){
        $value = isset($this->xmlData->ChangeLineStatusResult) ? $this->xmlData->ChangeLineStatusResult : false;
        return new VoidOtapiAnswer($value);
    }
}