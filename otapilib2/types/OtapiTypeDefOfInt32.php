<?php

class OtapiTypeDefOfInt32 extends BaseOtapiType{
    /**
     * @return int
     */
    public function GetValue(){
        $value = isset($this->xmlData->Value) ? (string)$this->xmlData->Value : false;
        $propertyType = 'int';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
}