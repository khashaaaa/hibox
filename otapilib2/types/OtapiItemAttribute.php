<?php

class OtapiItemAttribute extends BaseOtapiType{
    /**
     * @return string
     */
    public function GetPropertyName(){
        $value = isset($this->xmlData->PropertyName) ? (string)$this->xmlData->PropertyName : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetValue(){
        $value = isset($this->xmlData->Value) ? (string)$this->xmlData->Value : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetValueAlias(){
        $value = isset($this->xmlData->ValueAlias) ? (string)$this->xmlData->ValueAlias : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetOriginalPropertyName(){
        $value = isset($this->xmlData->OriginalPropertyName) ? (string)$this->xmlData->OriginalPropertyName : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetOriginalValue(){
        $value = isset($this->xmlData->OriginalValue) ? (string)$this->xmlData->OriginalValue : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetOriginalValueAlias(){
        $value = isset($this->xmlData->OriginalValueAlias) ? (string)$this->xmlData->OriginalValueAlias : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return boolean
     */
    public function IsConfigurator(){
        $value = isset($this->xmlData->IsConfigurator) ? (string)$this->xmlData->IsConfigurator : false;
        $propertyType = 'boolean';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetImageUrl(){
        $value = isset($this->xmlData->ImageUrl) ? (string)$this->xmlData->ImageUrl : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetMiniImageUrl(){
        $value = isset($this->xmlData->MiniImageUrl) ? (string)$this->xmlData->MiniImageUrl : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
    * @return string
    */
    public function GetPidAttribute(){
        $attributes = $this->xmlData->attributes() ? $this->xmlData->attributes() : array();
        $value = isset($attributes['Pid']) ? (string)$attributes['Pid'] : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
    * @return string
    */
    public function GetVidAttribute(){
        $attributes = $this->xmlData->attributes() ? $this->xmlData->attributes() : array();
        $value = isset($attributes['Vid']) ? (string)$attributes['Vid'] : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
}