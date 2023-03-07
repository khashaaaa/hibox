<?php

class OtapiVendorRating extends BaseOtapiType{
    /**
     * @return int
     */
    public function GetLevel(){
        $value = isset($this->xmlData->Level) ? (string)$this->xmlData->Level : false;
        $propertyType = 'int';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return long
     */
    public function GetScore(){
        $value = isset($this->xmlData->Score) ? (string)$this->xmlData->Score : false;
        $propertyType = 'long';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return long
     */
    public function GetTotalFeedbacks(){
        $value = isset($this->xmlData->TotalFeedbacks) ? (string)$this->xmlData->TotalFeedbacks : false;
        $propertyType = 'long';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return long
     */
    public function GetPositiveFeedbacks(){
        $value = isset($this->xmlData->PositiveFeedbacks) ? (string)$this->xmlData->PositiveFeedbacks : false;
        $propertyType = 'long';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
}