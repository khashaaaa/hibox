<?php

class OtapiGetAccountInfoResponse extends BaseOtapiType{
    /**
     * @return OtapiAccountInfoAnswer
     */
    public function GetGetAccountInfoResult(){
        $value = isset($this->xmlData->GetAccountInfoResult) ? $this->xmlData->GetAccountInfoResult : false;
        return new OtapiAccountInfoAnswer($value);
    }
}