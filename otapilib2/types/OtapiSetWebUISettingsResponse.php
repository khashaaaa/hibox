<?php

class OtapiSetWebUISettingsResponse extends BaseOtapiType{
    /**
     * @return VoidOtapiAnswer
     */
    public function GetSetWebUISettingsResult(){
        $value = isset($this->xmlData->SetWebUISettingsResult) ? $this->xmlData->SetWebUISettingsResult : false;
        return new VoidOtapiAnswer($value);
    }
}