<?php

class OtapiSimplifiedItemConfigurationPropertyList extends BaseOtapiType{
    /**
     * @return OtapiSimplifiedItemConfigurationPropertyInfo[]
     */
    public function GetProperty(){
        return isset($this->xmlData->Property) ? new UnboundedElementsIterator(
                $this->xmlData->Property,
                array(
                    'type' => 'complexType',
                    'name' => 'OtapiSimplifiedItemConfigurationPropertyInfo'
                )
            ) : array();
    }
    /**
    * @return string
    */
    public function GetMultiInputPropertyIdAttribute(){
        $attributes = $this->xmlData->attributes() ? $this->xmlData->attributes() : array();
        $value = isset($attributes['MultiInputPropertyId']) ? (string)$attributes['MultiInputPropertyId'] : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
}