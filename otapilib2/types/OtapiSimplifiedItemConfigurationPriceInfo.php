<?php

class OtapiSimplifiedItemConfigurationPriceInfo extends OtapiSimplifiedItemPriceInfo{
    /**
     * @return boolean
     */
    public function IsFullConfiguration(){
        $value = isset($this->xmlData->IsFullConfiguration) ? (string)$this->xmlData->IsFullConfiguration : false;
        $propertyType = 'boolean';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetConfigurationId(){
        $value = isset($this->xmlData->ConfigurationId) ? (string)$this->xmlData->ConfigurationId : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return long
     */
    public function GetAvailableQuantity(){
        $value = isset($this->xmlData->AvailableQuantity) ? (string)$this->xmlData->AvailableQuantity : false;
        $propertyType = 'long';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
}