<?php

class OtapiContentMenuItemInfo extends BaseOtapiType{
    /**
     * @return int
     */
    public function GetId(){
        $value = isset($this->xmlData->Id) ? (string)$this->xmlData->Id : false;
        $propertyType = 'int';
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
     * @return string
     */
    public function GetAlias(){
        $value = isset($this->xmlData->Alias) ? (string)$this->xmlData->Alias : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
}