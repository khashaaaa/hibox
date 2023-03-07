<?php

class OtapiBasketCheckingMessage extends BaseOtapiType{
    /**
     * @return OtapiElementId
     */
    public function GetElementId(){
        $value = isset($this->xmlData->ElementId) ? $this->xmlData->ElementId : false;
        return new OtapiElementId($value);
    }
    /**
     * @return string
     */
    public function GetText(){
        $value = isset($this->xmlData->Text) ? (string)$this->xmlData->Text : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return OtapiNamedParameters
     */
    public function GetParameters(){
        $value = isset($this->xmlData->Parameters) ? $this->xmlData->Parameters : false;
        return new OtapiNamedParameters($value);
    }
    /**
     * @return string
     */
    public function GetStatus(){
        $value = isset($this->xmlData->Status) ? (string)$this->xmlData->Status : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetCode(){
        $value = isset($this->xmlData->Code) ? (string)$this->xmlData->Code : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
}