<?php

class OtapiLanguageInfo extends OtapiNamedProperty{
    /**
     * @return string
     */
    public function GetImageUrl(){
        $value = isset($this->xmlData->ImageUrl) ? (string)$this->xmlData->ImageUrl : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
}