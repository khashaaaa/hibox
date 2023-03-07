<?php

class OtapiRunOrderExportingToProviderResponse extends BaseOtapiType{
    /**
     * @return OtapiBackgroundActivityIdentificationInfoAnswer
     */
    public function GetRunOrderExportingToProviderResult(){
        $value = isset($this->xmlData->RunOrderExportingToProviderResult) ? $this->xmlData->RunOrderExportingToProviderResult : false;
        return new OtapiBackgroundActivityIdentificationInfoAnswer($value);
    }
}