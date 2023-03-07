<?php

class OtapiFeatureInfo extends BaseOtapiType{
    /**
     * @return string
     */
    public function GetName(){
        $value = isset($this->xmlData->Name) ? (string)$this->xmlData->Name : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetDescription(){
        $value = isset($this->xmlData->Description) ? (string)$this->xmlData->Description : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return OtapiArrayOfString1
     */
    public function GetTypes(){
        $value = isset($this->xmlData->Types) ? $this->xmlData->Types : false;
        return new OtapiArrayOfString1($value);
    }
}