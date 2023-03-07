<?php

class OtapiGetProviderInfoResponse extends BaseOtapiType{
    /**
     * @return OtapiProviderInfoAnswer
     */
    public function GetGetProviderInfoResult(){
        $value = isset($this->xmlData->GetProviderInfoResult) ? $this->xmlData->GetProviderInfoResult : false;
        return new OtapiProviderInfoAnswer($value);
    }
}