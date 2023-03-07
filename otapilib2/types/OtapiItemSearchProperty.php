<?php

class OtapiItemSearchProperty extends BaseOtapiType{
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
     * @return boolean
     */
    public function IsBrand(){
        $value = isset($this->xmlData->IsBrand) ? (string)$this->xmlData->IsBrand : false;
        $propertyType = 'boolean';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return boolean
     */
    public function IsCategory(){
        $value = isset($this->xmlData->IsCategory) ? (string)$this->xmlData->IsCategory : false;
        $propertyType = 'boolean';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return ArrayOfOtapiItemPropertyValue
     */
    public function GetValues(){
        $value = isset($this->xmlData->Values) ? $this->xmlData->Values : false;
        return new ArrayOfOtapiItemPropertyValue($value);
    }
}