<?php

class OtapiCurrencyInfo extends BaseOtapiType{
    /**
     * @return string
     */
    public function GetCode(){
        $value = isset($this->xmlData->Code) ? (string)$this->xmlData->Code : false;
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
     * @return string
     */
    public function GetSign(){
        $value = isset($this->xmlData->Sign) ? (string)$this->xmlData->Sign : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
}