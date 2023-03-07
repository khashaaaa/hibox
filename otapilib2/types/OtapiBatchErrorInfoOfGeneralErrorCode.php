<?php

class OtapiBatchErrorInfoOfGeneralErrorCode extends BaseOtapiType{
    /**
     * @return string
     */
    public function GetErrorCode(){
        $value = isset($this->xmlData->ErrorCode) ? (string)$this->xmlData->ErrorCode : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetErrorDescription(){
        $value = isset($this->xmlData->ErrorDescription) ? (string)$this->xmlData->ErrorDescription : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
}