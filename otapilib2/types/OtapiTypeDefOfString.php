<?php

class OtapiTypeDefOfString extends BaseOtapiType{
    /**
     * @return string
     */
    public function GetValue(){
        $value = isset($this->xmlData->Value) ? (string)$this->xmlData->Value : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
}