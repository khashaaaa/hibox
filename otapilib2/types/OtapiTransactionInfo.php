<?php

class OtapiTransactionInfo extends BaseOtapiType{
    /**
     * @return OtapiUserId
     */
    public function GetUserId(){
        $value = isset($this->xmlData->UserId) ? $this->xmlData->UserId : false;
        return new OtapiUserId($value);
    }
    /**
     * @return dateTime
     */
    public function GetTransactionDate(){
        $value = isset($this->xmlData->TransactionDate) ? (string)$this->xmlData->TransactionDate : false;
        $propertyType = 'dateTime';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetCurrencyCode(){
        $value = isset($this->xmlData->CurrencyCode) ? (string)$this->xmlData->CurrencyCode : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetCurrencySign(){
        $value = isset($this->xmlData->CurrencySign) ? (string)$this->xmlData->CurrencySign : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return decimal
     */
    public function GetAmount(){
        $value = isset($this->xmlData->Amount) ? (string)$this->xmlData->Amount : false;
        $propertyType = 'decimal';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetOrderId(){
        $value = isset($this->xmlData->OrderId) ? (string)$this->xmlData->OrderId : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetComment(){
        $value = isset($this->xmlData->Comment) ? (string)$this->xmlData->Comment : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetDirection(){
        $value = isset($this->xmlData->Direction) ? (string)$this->xmlData->Direction : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return OtapiTransactionTypeInfo
     */
    public function GetTransactionType(){
        $value = isset($this->xmlData->TransactionType) ? $this->xmlData->TransactionType : false;
        return new OtapiTransactionTypeInfo($value);
    }
    /**
     * @return OtapiBaseUserInfo
     */
    public function GetUserInfo(){
        $value = isset($this->xmlData->UserInfo) ? $this->xmlData->UserInfo : false;
        return new OtapiBaseUserInfo($value);
    }
}