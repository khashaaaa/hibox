<?php

class OtapiEditablePriceFormationSettingsAnswer extends OtapiAnswer{
    /**
     * @return OtapiEditablePriceFormationSettings
     */
    public function GetResult(){
        $value = isset($this->xmlData->Result) ? $this->xmlData->Result : false;
        return new OtapiEditablePriceFormationSettings($value);
    }
}