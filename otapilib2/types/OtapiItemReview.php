<?php

class OtapiItemReview extends BaseOtapiType{
    /**
     * @return string
     */
    public function GetSubOrderId(){
        $value = isset($this->xmlData->SubOrderId) ? (string)$this->xmlData->SubOrderId : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetTransactionId(){
        $value = isset($this->xmlData->TransactionId) ? (string)$this->xmlData->TransactionId : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetContent(){
        $value = isset($this->xmlData->Content) ? (string)$this->xmlData->Content : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
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
     * @return string
     */
    public function GetUserNick(){
        $value = isset($this->xmlData->UserNick) ? (string)$this->xmlData->UserNick : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetReply(){
        $value = isset($this->xmlData->Reply) ? (string)$this->xmlData->Reply : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetUserRole(){
        $value = isset($this->xmlData->UserRole) ? (string)$this->xmlData->UserRole : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetResult(){
        $value = isset($this->xmlData->Result) ? (string)$this->xmlData->Result : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
}