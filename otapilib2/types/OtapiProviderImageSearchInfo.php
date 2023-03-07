<?php

class OtapiProviderImageSearchInfo extends BaseOtapiType{
    /**
     * @return boolean
     */
    public function IsAvailable(){
        $value = isset($this->xmlData->IsAvailable) ? (string)$this->xmlData->IsAvailable : false;
        $propertyType = 'boolean';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return boolean
     */
    public function IsEnabled(){
        $value = isset($this->xmlData->IsEnabled) ? (string)$this->xmlData->IsEnabled : false;
        $propertyType = 'boolean';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
}