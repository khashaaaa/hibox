<?php

class OtapiSize extends BaseOtapiType{
    /**
     * @return int
     */
    public function GetLength(){
        $value = isset($this->xmlData->Length) ? (string)$this->xmlData->Length : false;
        $propertyType = 'int';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return int
     */
    public function GetHeight(){
        $value = isset($this->xmlData->Height) ? (string)$this->xmlData->Height : false;
        $propertyType = 'int';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return int
     */
    public function GetWidth(){
        $value = isset($this->xmlData->Width) ? (string)$this->xmlData->Width : false;
        $propertyType = 'int';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
}