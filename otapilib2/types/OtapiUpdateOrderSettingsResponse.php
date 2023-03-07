<?php

class OtapiUpdateOrderSettingsResponse extends BaseOtapiType{
    /**
     * @return VoidOtapiAnswer
     */
    public function GetUpdateOrderSettingsResult(){
        $value = isset($this->xmlData->UpdateOrderSettingsResult) ? $this->xmlData->UpdateOrderSettingsResult : false;
        return new VoidOtapiAnswer($value);
    }
}