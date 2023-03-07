<?php

class OtapiBillInfo extends BaseOtapiType{
    /**
     * @return OtapiBillId
     */
    public function GetId(){
        $value = isset($this->xmlData->Id) ? $this->xmlData->Id : false;
        return new OtapiBillId($value);
    }
    /**
     * @return dateTime
     */
    public function GetCreationDate(){
        $value = isset($this->xmlData->CreationDate) ? (string)$this->xmlData->CreationDate : false;
        $propertyType = 'dateTime';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return dateTime
     */
    public function GetPaymentDate(){
        $value = isset($this->xmlData->PaymentDate) ? (string)$this->xmlData->PaymentDate : false;
        $propertyType = 'dateTime';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetPaymentUrl(){
        $value = isset($this->xmlData->PaymentUrl) ? (string)$this->xmlData->PaymentUrl : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return OtapiMoney
     */
    public function GetPaidSumInUSD(){
        $value = isset($this->xmlData->PaidSumInUSD) ? $this->xmlData->PaidSumInUSD : false;
        return new OtapiMoney($value);
    }
    /**
     * @return OtapiMoney
     */
    public function GetSumToPayInUSD(){
        $value = isset($this->xmlData->SumToPayInUSD) ? $this->xmlData->SumToPayInUSD : false;
        return new OtapiMoney($value);
    }
    /**
     * @return OtapiMoney
     */
    public function GetSumToPayInRUB(){
        $value = isset($this->xmlData->SumToPayInRUB) ? $this->xmlData->SumToPayInRUB : false;
        return new OtapiMoney($value);
    }
    /**
     * @return OtapiBillStatusInfo
     */
    public function GetStatus(){
        $value = isset($this->xmlData->Status) ? $this->xmlData->Status : false;
        return new OtapiBillStatusInfo($value);
    }
    /**
     * @return OtapiBillTypeInfo
     */
    public function GetType(){
        $value = isset($this->xmlData->Type) ? $this->xmlData->Type : false;
        return new OtapiBillTypeInfo($value);
    }
    /**
     * @return OtapiDatePeriod
     */
    public function GetSettlingPeriod(){
        $value = isset($this->xmlData->SettlingPeriod) ? $this->xmlData->SettlingPeriod : false;
        return new OtapiDatePeriod($value);
    }
    /**
     * @return string
     */
    public function GetDescription(){
        $value = isset($this->xmlData->Description) ? (string)$this->xmlData->Description : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
}