<?php

class OtapiTariffHistoryElement extends BaseOtapiType{
    /**
     * @return int
     */
    public function GetId(){
        $value = isset($this->xmlData->Id) ? (string)$this->xmlData->Id : false;
        $propertyType = 'int';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return dateTime
     */
    public function GetActivationDate(){
        $value = isset($this->xmlData->ActivationDate) ? (string)$this->xmlData->ActivationDate : false;
        $propertyType = 'dateTime';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return OtapiTariffInfo
     */
    public function GetTariff(){
        $value = isset($this->xmlData->Tariff) ? $this->xmlData->Tariff : false;
        return new OtapiTariffInfo($value);
    }
    /**
     * @return OtapiInstanceBaseInfo
     */
    public function GetLogisticianInstance(){
        $value = isset($this->xmlData->LogisticianInstance) ? $this->xmlData->LogisticianInstance : false;
        return new OtapiInstanceBaseInfo($value);
    }
    /**
     * @return boolean
     */
    public function IsActive(){
        $value = isset($this->xmlData->IsActive) ? (string)$this->xmlData->IsActive : false;
        $propertyType = 'boolean';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
}