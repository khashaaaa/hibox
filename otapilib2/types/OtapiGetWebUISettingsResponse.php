<?php

class OtapiGetWebUISettingsResponse extends BaseOtapiType{
    /**
     * @return OtapiWebUISettingsAnswer
     */
    public function GetGetWebUISettingsResult(){
        $value = isset($this->xmlData->GetWebUISettingsResult) ? $this->xmlData->GetWebUISettingsResult : false;
        return new OtapiWebUISettingsAnswer($value);
    }
}