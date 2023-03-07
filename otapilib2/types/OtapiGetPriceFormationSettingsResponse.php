<?php

class OtapiGetPriceFormationSettingsResponse extends BaseOtapiType{
    /**
     * @return OtapiEditablePriceFormationSettingsAnswer
     */
    public function GetGetPriceFormationSettingsResult(){
        $value = isset($this->xmlData->GetPriceFormationSettingsResult) ? $this->xmlData->GetPriceFormationSettingsResult : false;
        return new OtapiEditablePriceFormationSettingsAnswer($value);
    }
}