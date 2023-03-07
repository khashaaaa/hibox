<?php

class OtapiDeleteAccountResponse extends BaseOtapiType{
    /**
     * @return VoidOtapiAnswer
     */
    public function GetDeleteAccountResult(){
        $value = isset($this->xmlData->DeleteAccountResult) ? $this->xmlData->DeleteAccountResult : false;
        return new VoidOtapiAnswer($value);
    }
}