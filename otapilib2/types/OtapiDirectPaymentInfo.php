<?php

class OtapiDirectPaymentInfo extends BaseOtapiType{
    /**
     * @return string
     */
    public function GetPaymentId(){
        $value = isset($this->xmlData->PaymentId) ? (string)$this->xmlData->PaymentId : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetPaymentModeId(){
        $value = isset($this->xmlData->PaymentModeId) ? (string)$this->xmlData->PaymentModeId : false;
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
    public function GetUserFullName(){
        $value = isset($this->xmlData->UserFullName) ? (string)$this->xmlData->UserFullName : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetUserLogin(){
        $value = isset($this->xmlData->UserLogin) ? (string)$this->xmlData->UserLogin : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
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
    public function GetCreatedDate(){
        $value = isset($this->xmlData->CreatedDate) ? (string)$this->xmlData->CreatedDate : false;
        $propertyType = 'dateTime';
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
    public function GetCurrencyCode(){
        $value = isset($this->xmlData->CurrencyCode) ? (string)$this->xmlData->CurrencyCode : false;
        $propertyType = 'string';
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
    public function GetFileUrl(){
        $value = isset($this->xmlData->FileUrl) ? (string)$this->xmlData->FileUrl : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
}