<?php

class OtapiGetItemInfoResponse extends BaseOtapiType{
    /**
     * @return OtapiItemInfoAnswer
     */
    public function GetGetItemInfoResult(){
        $value = isset($this->xmlData->GetItemInfoResult) ? $this->xmlData->GetItemInfoResult : false;
        return new OtapiItemInfoAnswer($value);
    }
}