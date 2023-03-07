<?php

class OtapiSelectorExportersSettingsAnswer extends OtapiAnswer{
    /**
     * @return OtapiSelectorExportersSettings
     */
    public function GetResult(){
        $value = isset($this->xmlData->Result) ? $this->xmlData->Result : false;
        return new OtapiSelectorExportersSettings($value);
    }
}