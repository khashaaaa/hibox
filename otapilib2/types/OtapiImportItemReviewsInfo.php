<?php

class OtapiImportItemReviewsInfo extends BaseOtapiType{
    /**
     * @return int
     */
    public function GetSuccessfullyNumber(){
        $value = isset($this->xmlData->SuccessfullyNumber) ? (string)$this->xmlData->SuccessfullyNumber : false;
        $propertyType = 'int';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return int
     */
    public function GetIncorrectNumber(){
        $value = isset($this->xmlData->IncorrectNumber) ? (string)$this->xmlData->IncorrectNumber : false;
        $propertyType = 'int';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return int
     */
    public function GetDuplicateNumber(){
        $value = isset($this->xmlData->DuplicateNumber) ? (string)$this->xmlData->DuplicateNumber : false;
        $propertyType = 'int';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
}