<?php

class OtapiSocialNetworkSettingsAnswer extends OtapiAnswer{
    /**
     * @return OtapiSocialNetworkSettings
     */
    public function GetResult(){
        $value = isset($this->xmlData->Result) ? $this->xmlData->Result : false;
        return new OtapiSocialNetworkSettings($value);
    }
}