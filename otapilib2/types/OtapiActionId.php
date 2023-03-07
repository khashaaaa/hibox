<?php

class OtapiActionId extends OtapiBaseSelfErrorCheck{
    /**
    * @return int
    */
    public function GetIdAttribute(){
        $attributes = $this->xmlData->attributes() ? $this->xmlData->attributes() : array();
        $value = isset($attributes['Id']) ? (string)$attributes['Id'] : false;
        $propertyType = 'int';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
    * @return ActionType
    */
    public function GetTypeAttribute(){
        $attributes = $this->xmlData->attributes() ? $this->xmlData->attributes() : array();
        $value = isset($attributes['Type']) ? (string)$attributes['Type'] : false;
        $propertyType = 'ActionType';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
}