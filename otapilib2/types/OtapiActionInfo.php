<?php

class OtapiActionInfo extends BaseOtapiType{
    /**
     * @return OtapiActionId
     */
    public function GetId(){
        $value = isset($this->xmlData->Id) ? $this->xmlData->Id : false;
        return new OtapiActionId($value);
    }
    /**
     * @return boolean
     */
    public function IsRequired(){
        $value = isset($this->xmlData->IsRequired) ? (string)$this->xmlData->IsRequired : false;
        $propertyType = 'boolean';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return OtapiArrayOfActionInfo
     */
    public function GetChildren(){
        $value = isset($this->xmlData->Children) ? $this->xmlData->Children : false;
        return new OtapiArrayOfActionInfo($value);
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
    public function GetDescriptionAttribute(){
        $attributes = $this->xmlData->attributes() ? $this->xmlData->attributes() : array();
        $value = isset($attributes['Description']) ? (string)$attributes['Description'] : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
    * @return boolean
    */
    public function EnabledAttribute(){
        $attributes = $this->xmlData->attributes() ? $this->xmlData->attributes() : array();
        $value = isset($attributes['Enabled']) ? (string)$attributes['Enabled'] : false;
        $propertyType = 'boolean';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
}