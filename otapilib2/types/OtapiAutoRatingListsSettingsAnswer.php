<?php

class OtapiAutoRatingListsSettingsAnswer extends OtapiAnswer{
    /**
     * @return OtapiAutoRatingListsSettings
     */
    public function GetResult(){
        $value = isset($this->xmlData->Result) ? $this->xmlData->Result : false;
        return new OtapiAutoRatingListsSettings($value);
    }
}