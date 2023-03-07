<?php

class OtapiGetInstanceOptionsInfoResponse extends BaseOtapiType{
    /**
     * @return OtapiInstanceOptionsInfoAnswer
     */
    public function GetGetInstanceOptionsInfoResult(){
        $value = isset($this->xmlData->GetInstanceOptionsInfoResult) ? $this->xmlData->GetInstanceOptionsInfoResult : false;
        return new OtapiInstanceOptionsInfoAnswer($value);
    }
}