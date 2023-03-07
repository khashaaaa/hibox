<?php

class OtapiPaymentMode extends BaseOtapiType{
    /**
     * @return string
     */
    public function GetId(){
        $value = isset($this->xmlData->Id) ? (string)$this->xmlData->Id : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
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
     * @return string
     */
    public function GetDescription(){
        $value = isset($this->xmlData->Description) ? (string)$this->xmlData->Description : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetPaymSortCode(){
        $value = isset($this->xmlData->PaymSortCode) ? (string)$this->xmlData->PaymSortCode : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetPaymSortText(){
        $value = isset($this->xmlData->PaymSortText) ? (string)$this->xmlData->PaymSortText : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetImageURL(){
        $value = isset($this->xmlData->ImageURL) ? (string)$this->xmlData->ImageURL : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetAbsoluteImageUrl(){
        $value = isset($this->xmlData->AbsoluteImageUrl) ? (string)$this->xmlData->AbsoluteImageUrl : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetCustomField(){
        $value = isset($this->xmlData->CustomField) ? (string)$this->xmlData->CustomField : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetPaymentSystem(){
        $value = isset($this->xmlData->PaymentSystem) ? (string)$this->xmlData->PaymentSystem : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return int
     */
    public function GetSortOrder(){
        $value = isset($this->xmlData->SortOrder) ? (string)$this->xmlData->SortOrder : false;
        $propertyType = 'int';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
}