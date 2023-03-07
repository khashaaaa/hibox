<?php

class OtapiSimplifiedItemConfigurationMultiInputInfo extends OtapiSimplifiedItemConfigurationPriceInfo{
    /**
    * @return string
    */
    public function GetInputPropertyIdAttribute(){
        $attributes = $this->xmlData->attributes() ? $this->xmlData->attributes() : array();
        $value = isset($attributes['InputPropertyId']) ? (string)$attributes['InputPropertyId'] : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
    * @return string
    */
    public function GetInputPropertyValueIdAttribute(){
        $attributes = $this->xmlData->attributes() ? $this->xmlData->attributes() : array();
        $value = isset($attributes['InputPropertyValueId']) ? (string)$attributes['InputPropertyValueId'] : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
}