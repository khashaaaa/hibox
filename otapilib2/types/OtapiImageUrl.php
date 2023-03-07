<?php

class OtapiImageUrl extends BaseOtapiType{
    /**
    * @return int
    */
    public function GetWidthAttribute(){
        $attributes = $this->xmlData->attributes() ? $this->xmlData->attributes() : array();
        $value = isset($attributes['Width']) ? (string)$attributes['Width'] : false;
        $propertyType = 'int';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
    * @return int
    */
    public function GetHeightAttribute(){
        $attributes = $this->xmlData->attributes() ? $this->xmlData->attributes() : array();
        $value = isset($attributes['Height']) ? (string)$attributes['Height'] : false;
        $propertyType = 'int';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
}