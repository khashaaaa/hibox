<?php

class OtapiDeliveryServiceSystemSettingsAnswer extends OtapiAnswer{
    /**
     * @return OtapiDeliveryServiceSystemSettings
     */
    public function GetResult(){
        $value = isset($this->xmlData->Result) ? $this->xmlData->Result : false;
        return new OtapiDeliveryServiceSystemSettings($value);
    }
}