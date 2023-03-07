<?php

class OtapiSetShowcaseSettingsResponse extends BaseOtapiType{
    /**
     * @return VoidOtapiAnswer
     */
    public function GetSetShowcaseSettingsResult(){
        $value = isset($this->xmlData->SetShowcaseSettingsResult) ? $this->xmlData->SetShowcaseSettingsResult : false;
        return new VoidOtapiAnswer($value);
    }
}