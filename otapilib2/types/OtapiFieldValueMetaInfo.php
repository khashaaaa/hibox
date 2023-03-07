<?php

class OtapiFieldValueMetaInfo extends BaseOtapiType{
    /**
     * @return OtapiArrayOfFieldValueMetaInfo
     */
    public function GetChildren(){
        $value = isset($this->xmlData->Children) ? $this->xmlData->Children : false;
        return new OtapiArrayOfFieldValueMetaInfo($value);
    }
    /**
    * @return string
    */
    public function GetNameAttribute(){
        $attributes = $this->xmlData->attributes() ? $this->xmlData->attributes() : array();
        $value = isset($attributes['Name']) ? (string)$attributes['Name'] : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
    * @return string
    */
    public function GetDisplayNameAttribute(){
        $attributes = $this->xmlData->attributes() ? $this->xmlData->attributes() : array();
        $value = isset($attributes['DisplayName']) ? (string)$attributes['DisplayName'] : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
    * @return string
    */
    public function GetDescriptionAttribute(){
        $attributes = $this->xmlData->attributes() ? $this->xmlData->attributes() : array();
        $value = isset($attributes['Description']) ? (string)$attributes['Description'] : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
    * @return boolean
    */
    public function IsReadOnlyAttribute(){
        $attributes = $this->xmlData->attributes() ? $this->xmlData->attributes() : array();
        $value = isset($attributes['IsReadOnly']) ? (string)$attributes['IsReadOnly'] : false;
        $propertyType = 'boolean';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
}