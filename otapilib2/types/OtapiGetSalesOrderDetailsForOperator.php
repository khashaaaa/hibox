<?php

class OtapiGetSalesOrderDetailsForOperator extends BaseOtapiType{
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
    public function GetLanguage(){
        $value = isset($this->xmlData->language) ? (string)$this->xmlData->language : false;
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
    public function GetSalesId(){
        $value = isset($this->xmlData->salesId) ? (string)$this->xmlData->salesId : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetFilter(){
        $value = isset($this->xmlData->filter) ? (string)$this->xmlData->filter : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return int
     */
    public function GetQueryType(){
        $value = isset($this->xmlData->queryType) ? (string)$this->xmlData->queryType : false;
        $propertyType = 'int';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
}