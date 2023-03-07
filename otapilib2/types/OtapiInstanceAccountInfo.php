<?php

class OtapiInstanceAccountInfo extends BaseOtapiType{
    /**
     * @return decimal
     */
    public function GetPrepayment(){
        $value = isset($this->xmlData->Prepayment) ? (string)$this->xmlData->Prepayment : false;
        $propertyType = 'decimal';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return decimal
     */
    public function GetDebt(){
        $value = isset($this->xmlData->Debt) ? (string)$this->xmlData->Debt : false;
        $propertyType = 'decimal';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
}