<?php

class OtapiUpdateInstanceRole extends BaseOtapiType{
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
    public function GetRoleName(){
        $value = isset($this->xmlData->roleName) ? (string)$this->xmlData->roleName : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetXmlUpdateData(){
        $value = isset($this->xmlData->xmlUpdateData) ? (string)$this->xmlData->xmlUpdateData : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
}