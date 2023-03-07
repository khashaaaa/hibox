<?php

class OtapiSimplifiedItemConfigurationPropertyInfo extends BaseOtapiType{
    /**
     * @return OtapiSimplifiedItemConfigurationPropertyValueInfo[]
     */
    public function GetValue(){
        return isset($this->xmlData->Value) ? new UnboundedElementsIterator(
                $this->xmlData->Value,
                array(
                    'type' => 'complexType',
                    'name' => 'OtapiSimplifiedItemConfigurationPropertyValueInfo'
                )
            ) : array();
    }
    /**
    * @return string
    */
    public function GetIdAttribute(){
        $attributes = $this->xmlData->attributes() ? $this->xmlData->attributes() : array();
        $value = isset($attributes['Id']) ? (string)$attributes['Id'] : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
}