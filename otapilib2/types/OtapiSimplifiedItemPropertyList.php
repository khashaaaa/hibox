<?php

class OtapiSimplifiedItemPropertyList extends BaseOtapiType{
    /**
     * @return OtapiSimplifiedItemProperty[]
     */
    public function GetProperty(){
        return isset($this->xmlData->Property) ? new UnboundedElementsIterator(
                $this->xmlData->Property,
                array(
                    'type' => 'complexType',
                    'name' => 'OtapiSimplifiedItemProperty'
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