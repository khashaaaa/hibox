<?php

class OtapiProviderSearchSortInfo extends BaseOtapiType{
    /**
     * @return string
     */
    public function GetField(){
        $value = isset($this->xmlData->Field) ? (string)$this->xmlData->Field : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetDirection(){
        $value = isset($this->xmlData->Direction) ? (string)$this->xmlData->Direction : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetOrderBy(){
        $value = isset($this->xmlData->OrderBy) ? (string)$this->xmlData->OrderBy : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetDisplayName(){
        $value = isset($this->xmlData->DisplayName) ? (string)$this->xmlData->DisplayName : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
}