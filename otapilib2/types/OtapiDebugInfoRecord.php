<?php

class OtapiDebugInfoRecord extends BaseOtapiType{
    /**
     * @return OtapiDebugInfoRecord[]
     */
    public function GetRecord(){
        return isset($this->xmlData->Record) ? new UnboundedElementsIterator(
                $this->xmlData->Record,
                array(
                    'type' => 'complexType',
                    'name' => 'OtapiDebugInfoRecord'
                )
            ) : array();
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
    public function GetMessageAttribute(){
        $attributes = $this->xmlData->attributes() ? $this->xmlData->attributes() : array();
        $value = isset($attributes['Message']) ? (string)$attributes['Message'] : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
    * @return double
    */
    public function GetTimeAttribute(){
        $attributes = $this->xmlData->attributes() ? $this->xmlData->attributes() : array();
        $value = isset($attributes['Time']) ? (string)$attributes['Time'] : false;
        $propertyType = 'double';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
    * @return int
    */
    public function GetCountAttribute(){
        $attributes = $this->xmlData->attributes() ? $this->xmlData->attributes() : array();
        $value = isset($attributes['Count']) ? (string)$attributes['Count'] : false;
        $propertyType = 'int';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
    * @return boolean
    */
    public function ParallelAttribute(){
        $attributes = $this->xmlData->attributes() ? $this->xmlData->attributes() : array();
        $value = isset($attributes['Parallel']) ? (string)$attributes['Parallel'] : false;
        $propertyType = 'boolean';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
}