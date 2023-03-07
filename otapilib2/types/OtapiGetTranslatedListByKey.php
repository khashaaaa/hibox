<?php

class OtapiGetTranslatedListByKey extends BaseOtapiType{
    /**
     * @return string
     */
    public function GetInstanceKey(){
        $value = isset($this->xmlData->instanceKey) ? (string)$this->xmlData->instanceKey : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetSessionId(){
        $value = isset($this->xmlData->sessionId) ? (string)$this->xmlData->sessionId : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetKey(){
        $value = isset($this->xmlData->key) ? (string)$this->xmlData->key : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetIdInContext(){
        $value = isset($this->xmlData->idInContext) ? (string)$this->xmlData->idInContext : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
}