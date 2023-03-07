<?php

class OtapiGetProviderInfoListResponse extends BaseOtapiType{
    /**
     * @return OtapiProviderInfoListAnswer
     */
    public function GetGetProviderInfoListResult(){
        $value = isset($this->xmlData->GetProviderInfoListResult) ? $this->xmlData->GetProviderInfoListResult : false;
        return new OtapiProviderInfoListAnswer($value);
    }
}