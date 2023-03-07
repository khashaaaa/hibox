<?php

class OtapiGetShowcaseResponse extends BaseOtapiType{
    /**
     * @return OtapiShowcaseSettingsAnswer
     */
    public function GetGetShowcaseResult(){
        $value = isset($this->xmlData->GetShowcaseResult) ? $this->xmlData->GetShowcaseResult : false;
        return new OtapiShowcaseSettingsAnswer($value);
    }
}