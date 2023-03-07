<?php

class OtapiGetEnabledFeaturesResponse extends BaseOtapiType{
    /**
     * @return OtapiEnabledFeaturesAnswer
     */
    public function GetGetEnabledFeaturesResult(){
        $value = isset($this->xmlData->GetEnabledFeaturesResult) ? $this->xmlData->GetEnabledFeaturesResult : false;
        return new OtapiEnabledFeaturesAnswer($value);
    }
}