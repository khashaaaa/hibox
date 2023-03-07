<?php

class OtapiChangeEmailResponse extends BaseOtapiType{
    /**
     * @return OtapiEmailConfirmationInfoAnswer
     */
    public function GetChangeEmailResult(){
        $value = isset($this->xmlData->ChangeEmailResult) ? $this->xmlData->ChangeEmailResult : false;
        return new OtapiEmailConfirmationInfoAnswer($value);
    }
}