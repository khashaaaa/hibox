<?php

class OtapiConfirmEmailResponse extends BaseOtapiType{
    /**
     * @return OtapiSessionIdAnswer
     */
    public function GetConfirmEmailResult(){
        $value = isset($this->xmlData->ConfirmEmailResult) ? $this->xmlData->ConfirmEmailResult : false;
        return new OtapiSessionIdAnswer($value);
    }
}