<?php

class OtapiPriceFormationGroupInfo extends BaseOtapiType{
    /**
     * @return OtapiPriceFormationGroupId
     */
    public function GetId(){
        $value = isset($this->xmlData->Id) ? $this->xmlData->Id : false;
        return new OtapiPriceFormationGroupId($value);
    }
    /**
     * @return OtapiPriceFormationSettings
     */
    public function GetSettings(){
        $value = isset($this->xmlData->Settings) ? $this->xmlData->Settings : false;
        return new OtapiPriceFormationSettings($value);
    }
    /**
     * @return string
     */
    public function GetProviderType(){
        $value = isset($this->xmlData->ProviderType) ? (string)$this->xmlData->ProviderType : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
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
    public function IsDefaultAttribute(){
        $attributes = $this->xmlData->attributes() ? $this->xmlData->attributes() : array();
        $value = isset($attributes['IsDefault']) ? (string)$attributes['IsDefault'] : false;
        $propertyType = 'boolean';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
    * @return string
    */
    public function GetStrategyTypeAttribute(){
        $attributes = $this->xmlData->attributes() ? $this->xmlData->attributes() : array();
        $value = isset($attributes['StrategyType']) ? (string)$attributes['StrategyType'] : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
}