<?php

class OtapiAddPrices extends BaseOtapiType{
    /**
     * @return string
     */
    public function GetInstanceKey(){
        $value = isset($this->xmlData->instanceKey) ? (string)$this->xmlData->instanceKey : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetXmlFirst(){
        $value = isset($this->xmlData->xmlFirst) ? (string)$this->xmlData->xmlFirst : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetXmlSecond(){
        $value = isset($this->xmlData->xmlSecond) ? (string)$this->xmlData->xmlSecond : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
}