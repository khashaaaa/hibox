<?php

class OtapiGetProviderOrdersIntegrationSessionAuthenticationInfoResponse extends BaseOtapiType{
    /**
     * @return OtapiProviderAuthenticationInfoAnswer
     */
    public function GetGetProviderOrdersIntegrationSessionAuthenticationInfoResult(){
        $value = isset($this->xmlData->GetProviderOrdersIntegrationSessionAuthenticationInfoResult) ? $this->xmlData->GetProviderOrdersIntegrationSessionAuthenticationInfoResult : false;
        return new OtapiProviderAuthenticationInfoAnswer($value);
    }
}