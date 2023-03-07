<?php

class OtapiMetaEntityInfo extends BaseOtapiType{
    /**
     * @return string
     */
    public function GetName(){
        $value = isset($this->xmlData->Name) ? (string)$this->xmlData->Name : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetDisplayName(){
        $value = isset($this->xmlData->DisplayName) ? (string)$this->xmlData->DisplayName : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetGetMethod(){
        $value = isset($this->xmlData->GetMethod) ? (string)$this->xmlData->GetMethod : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetUpdateMethod(){
        $value = isset($this->xmlData->UpdateMethod) ? (string)$this->xmlData->UpdateMethod : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetUpdateDataRootName(){
        $value = isset($this->xmlData->UpdateDataRootName) ? (string)$this->xmlData->UpdateDataRootName : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return OtapiArrayOfString6
     */
    public function GetAdditionalParameters(){
        $value = isset($this->xmlData->AdditionalParameters) ? $this->xmlData->AdditionalParameters : false;
        return new OtapiArrayOfString6($value);
    }
}