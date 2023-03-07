<?php

class OtapiPackageInfo extends BaseOtapiType{
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
    public function GetStatusCode(){
        $value = isset($this->xmlData->StatusCode) ? (string)$this->xmlData->StatusCode : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetStatusName(){
        $value = isset($this->xmlData->StatusName) ? (string)$this->xmlData->StatusName : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetMailTrackingNum(){
        $value = isset($this->xmlData->MailTrackingNum) ? (string)$this->xmlData->MailTrackingNum : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return boolean
     */
    public function CanBeTracking(){
        $value = isset($this->xmlData->CanBeTracking) ? (string)$this->xmlData->CanBeTracking : false;
        $propertyType = 'boolean';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return decimal
     */
    public function GetWeight(){
        $value = isset($this->xmlData->Weight) ? (string)$this->xmlData->Weight : false;
        $propertyType = 'decimal';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return decimal
     */
    public function GetPrice(){
        $value = isset($this->xmlData->Price) ? (string)$this->xmlData->Price : false;
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
    public function GetCurrencySign(){
        $value = isset($this->xmlData->CurrencySign) ? (string)$this->xmlData->CurrencySign : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetDeliveryModeName(){
        $value = isset($this->xmlData->DeliveryModeName) ? (string)$this->xmlData->DeliveryModeName : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetDeliveryAddress(){
        $value = isset($this->xmlData->DeliveryAddress) ? (string)$this->xmlData->DeliveryAddress : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetDeliveryContactName(){
        $value = isset($this->xmlData->DeliveryContactName) ? (string)$this->xmlData->DeliveryContactName : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetDeliveryContactPhone(){
        $value = isset($this->xmlData->DeliveryContactPhone) ? (string)$this->xmlData->DeliveryContactPhone : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
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
     * @return string
     */
    public function GetAdditionalInfo(){
        $value = isset($this->xmlData->AdditionalInfo) ? (string)$this->xmlData->AdditionalInfo : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return OtapiSize
     */
    public function GetSize(){
        $value = isset($this->xmlData->Size) ? $this->xmlData->Size : false;
        return new OtapiSize($value);
    }
    /**
     * @return OtapiArrayOfPackageItem
     */
    public function GetItems(){
        $value = isset($this->xmlData->Items) ? $this->xmlData->Items : false;
        return new OtapiArrayOfPackageItem($value);
    }
    /**
     * @return string
     */
    public function GetDeliveryContactPassportNumber(){
        $value = isset($this->xmlData->DeliveryContactPassportNumber) ? (string)$this->xmlData->DeliveryContactPassportNumber : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetDeliveryContactRegistrationAddress(){
        $value = isset($this->xmlData->DeliveryContactRegistrationAddress) ? (string)$this->xmlData->DeliveryContactRegistrationAddress : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetDeliveryContactINN(){
        $value = isset($this->xmlData->DeliveryContactINN) ? (string)$this->xmlData->DeliveryContactINN : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
}