<?php

class OtapiCategoryImportInfo extends BaseOtapiType{
    /**
     * @return OtapiArrayOfCategoryImportInfo
     */
    public function GetChildren(){
        $value = isset($this->xmlData->Children) ? $this->xmlData->Children : false;
        return new OtapiArrayOfCategoryImportInfo($value);
    }
    /**
     * @return int
     */
    public function GetLocalId(){
        $value = isset($this->xmlData->LocalId) ? (string)$this->xmlData->LocalId : false;
        $propertyType = 'int';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetExternalId(){
        $value = isset($this->xmlData->ExternalId) ? (string)$this->xmlData->ExternalId : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return OtapiSearchItemsParameters
     */
    public function GetSearchItemsParameters(){
        $value = isset($this->xmlData->SearchItemsParameters) ? $this->xmlData->SearchItemsParameters : false;
        return new OtapiSearchItemsParameters($value);
    }
    /**
     * @return string
     */
    public function GetName(){
        $value = isset($this->xmlData->Name) ? (string)$this->xmlData->Name : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return decimal
     */
    public function GetApproxWeight(){
        $value = isset($this->xmlData->ApproxWeight) ? (string)$this->xmlData->ApproxWeight : false;
        $propertyType = 'decimal';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetProviderType(){
        $value = isset($this->xmlData->ProviderType) ? (string)$this->xmlData->ProviderType : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
    * @return boolean
    */
    public function IsDeletedAttribute(){
        $attributes = $this->xmlData->attributes() ? $this->xmlData->attributes() : array();
        $value = isset($attributes['IsDeleted']) ? (string)$attributes['IsDeleted'] : false;
        $propertyType = 'boolean';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
    * @return boolean
    */
    public function IsParentOnProviderAttribute(){
        $attributes = $this->xmlData->attributes() ? $this->xmlData->attributes() : array();
        $value = isset($attributes['IsParentOnProvider']) ? (string)$attributes['IsParentOnProvider'] : false;
        $propertyType = 'boolean';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
    * @return boolean
    */
    public function IsHiddenAttribute(){
        $attributes = $this->xmlData->attributes() ? $this->xmlData->attributes() : array();
        $value = isset($attributes['IsHidden']) ? (string)$attributes['IsHidden'] : false;
        $propertyType = 'boolean';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
}