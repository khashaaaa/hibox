<?php

class OtapiCurrencyInformation extends BaseOtapiType{
    /**
     * @return string
     */
    public function GetName(){
        $value = isset($this->xmlData->Name) ? (string)$this->xmlData->Name : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return boolean
     */
    public function IsUse(){
        $value = isset($this->xmlData->IsUse) ? (string)$this->xmlData->IsUse : false;
        $propertyType = 'boolean';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return decimal
     */
    public function GetRate(){
        $value = isset($this->xmlData->Rate) ? (string)$this->xmlData->Rate : false;
        $propertyType = 'decimal';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
}