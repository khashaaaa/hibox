<?php

class OtapiDirectPaymentModeSettings extends OtapiAbstractMetaListItem{
    /**
     * @return string
     */
    public function GetStartText(){
        $value = isset($this->xmlData->StartText) ? (string)$this->xmlData->StartText : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetFinishText(){
        $value = isset($this->xmlData->FinishText) ? (string)$this->xmlData->FinishText : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
}