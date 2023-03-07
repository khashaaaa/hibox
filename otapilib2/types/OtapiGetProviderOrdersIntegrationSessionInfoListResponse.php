<?php

class OtapiGetProviderOrdersIntegrationSessionInfoListResponse extends BaseOtapiType{
    /**
     * @return OtapiProviderSessionInfoListAnswer
     */
    public function GetGetProviderOrdersIntegrationSessionInfoListResult(){
        $value = isset($this->xmlData->GetProviderOrdersIntegrationSessionInfoListResult) ? $this->xmlData->GetProviderOrdersIntegrationSessionInfoListResult : false;
        return new OtapiProviderSessionInfoListAnswer($value);
    }
}