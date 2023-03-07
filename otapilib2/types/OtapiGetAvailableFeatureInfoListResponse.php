<?php

class OtapiGetAvailableFeatureInfoListResponse extends BaseOtapiType{
    /**
     * @return OtapiFeatureInfoListAnswer
     */
    public function GetGetAvailableFeatureInfoListResult(){
        $value = isset($this->xmlData->GetAvailableFeatureInfoListResult) ? $this->xmlData->GetAvailableFeatureInfoListResult : false;
        return new OtapiFeatureInfoListAnswer($value);
    }
}