<?php

class OtapiProviderSessionInfo extends BaseOtapiType{
    /**
     * @return string
     */
    public function GetId(){
        $value = isset($this->xmlData->Id) ? (string)$this->xmlData->Id : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetName(){
        $value = isset($this->xmlData->Name) ? (string)$this->xmlData->Name : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return OtapiProviderAccountBalance
     */
    public function GetBalance(){
        $value = isset($this->xmlData->Balance) ? $this->xmlData->Balance : false;
        return new OtapiProviderAccountBalance($value);
    }
}