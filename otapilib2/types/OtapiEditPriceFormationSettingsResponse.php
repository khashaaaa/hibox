<?php

class OtapiEditPriceFormationSettingsResponse extends BaseOtapiType{
    /**
     * @return VoidOtapiAnswer
     */
    public function GetEditPriceFormationSettingsResult(){
        $value = isset($this->xmlData->EditPriceFormationSettingsResult) ? $this->xmlData->EditPriceFormationSettingsResult : false;
        return new VoidOtapiAnswer($value);
    }
}