<?php

class OtapiGetConfiguredItemInfoListResponse extends BaseOtapiType{
    /**
     * @return OtapiConfiguredItemListAnswer
     */
    public function GetGetConfiguredItemInfoListResult(){
        $value = isset($this->xmlData->GetConfiguredItemInfoListResult) ? $this->xmlData->GetConfiguredItemInfoListResult : false;
        return new OtapiConfiguredItemListAnswer($value);
    }
}