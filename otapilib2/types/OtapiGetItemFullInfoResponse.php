<?php

class OtapiGetItemFullInfoResponse extends BaseOtapiType{
    /**
     * @return OtapiItemFullInfoAnswer
     */
    public function GetGetItemFullInfoResult(){
        $value = isset($this->xmlData->GetItemFullInfoResult) ? $this->xmlData->GetItemFullInfoResult : false;
        return new OtapiItemFullInfoAnswer($value);
    }
}