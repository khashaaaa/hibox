<?php

class OtapiItemPromotion extends OtapiAbstractCustomizablePrice{
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
    public function GetErrorCode(){
        $value = isset($this->xmlData->ErrorCode) ? (string)$this->xmlData->ErrorCode : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return boolean
     */
    public function HasError(){
        $value = isset($this->xmlData->HasError) ? (string)$this->xmlData->HasError : false;
        $propertyType = 'boolean';
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
    public function GetDesciption(){
        $value = isset($this->xmlData->Desciption) ? (string)$this->xmlData->Desciption : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetPromotionType(){
        $value = isset($this->xmlData->PromotionType) ? (string)$this->xmlData->PromotionType : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return dateTime
     */
    public function GetStartTime(){
        $value = isset($this->xmlData->StartTime) ? (string)$this->xmlData->StartTime : false;
        $propertyType = 'dateTime';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return dateTime
     */
    public function GetEndTime(){
        $value = isset($this->xmlData->EndTime) ? (string)$this->xmlData->EndTime : false;
        $propertyType = 'dateTime';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return OtapiPrice
     */
    public function GetPrice(){
        $value = isset($this->xmlData->Price) ? $this->xmlData->Price : false;
        return new OtapiPrice($value);
    }
    /**
     * @return string
     */
    public function GetOtherNeed(){
        $value = isset($this->xmlData->OtherNeed) ? (string)$this->xmlData->OtherNeed : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetOtherSend(){
        $value = isset($this->xmlData->OtherSend) ? (string)$this->xmlData->OtherSend : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return ArrayOfOtapiConfiguredItemPromotion
     */
    public function GetConfiguredItems(){
        $value = isset($this->xmlData->ConfiguredItems) ? $this->xmlData->ConfiguredItems : false;
        return new ArrayOfOtapiConfiguredItemPromotion($value);
    }
}