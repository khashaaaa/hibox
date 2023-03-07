<?php

class OtapiGetCommonInstanceOptionsInfoResponse extends BaseOtapiType{
    /**
     * @return OtapiCommonInstanceOptionsInfoAnswer
     */
    public function GetGetCommonInstanceOptionsInfoResult(){
        $value = isset($this->xmlData->GetCommonInstanceOptionsInfoResult) ? $this->xmlData->GetCommonInstanceOptionsInfoResult : false;
        return new OtapiCommonInstanceOptionsInfoAnswer($value);
    }
}