<?php

class OtapiUserInfo extends BaseOtapiType{
    /**
     * @return OtapiUserId
     */
    public function GetId(){
        $value = isset($this->xmlData->Id) ? $this->xmlData->Id : false;
        return new OtapiUserId($value);
    }
    /**
     * @return boolean
     */
    public function IsActive(){
        $value = isset($this->xmlData->IsActive) ? (string)$this->xmlData->IsActive : false;
        $propertyType = 'boolean';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return boolean
     */
    public function IsEmailVerified(){
        $value = isset($this->xmlData->IsEmailVerified) ? (string)$this->xmlData->IsEmailVerified : false;
        $propertyType = 'boolean';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetEmailConfirmationCode(){
        $value = isset($this->xmlData->EmailConfirmationCode) ? (string)$this->xmlData->EmailConfirmationCode : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetLogin(){
        $value = isset($this->xmlData->Login) ? (string)$this->xmlData->Login : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetFirstName(){
        $value = isset($this->xmlData->FirstName) ? (string)$this->xmlData->FirstName : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetLastName(){
        $value = isset($this->xmlData->LastName) ? (string)$this->xmlData->LastName : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetMiddleName(){
        $value = isset($this->xmlData->MiddleName) ? (string)$this->xmlData->MiddleName : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetSex(){
        $value = isset($this->xmlData->Sex) ? (string)$this->xmlData->Sex : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetEmail(){
        $value = isset($this->xmlData->Email) ? (string)$this->xmlData->Email : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetCountryCode(){
        $value = isset($this->xmlData->CountryCode) ? (string)$this->xmlData->CountryCode : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetCountry(){
        $value = isset($this->xmlData->Country) ? (string)$this->xmlData->Country : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetCity(){
        $value = isset($this->xmlData->City) ? (string)$this->xmlData->City : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetAddress(){
        $value = isset($this->xmlData->Address) ? (string)$this->xmlData->Address : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetPhone(){
        $value = isset($this->xmlData->Phone) ? (string)$this->xmlData->Phone : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetPostalCode(){
        $value = isset($this->xmlData->PostalCode) ? (string)$this->xmlData->PostalCode : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetRegion(){
        $value = isset($this->xmlData->Region) ? (string)$this->xmlData->Region : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetRecipientLastName(){
        $value = isset($this->xmlData->RecipientLastName) ? (string)$this->xmlData->RecipientLastName : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetRecipientFirstName(){
        $value = isset($this->xmlData->RecipientFirstName) ? (string)$this->xmlData->RecipientFirstName : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetRecipientMiddleName(){
        $value = isset($this->xmlData->RecipientMiddleName) ? (string)$this->xmlData->RecipientMiddleName : false;
        $propertyType = 'string';
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
     * @return dateTime
     */
    public function GetRegistrationDate(){
        $value = isset($this->xmlData->RegistrationDate) ? (string)$this->xmlData->RegistrationDate : false;
        $propertyType = 'dateTime';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return OtapiMoney
     */
    public function GetPurchaseVolume(){
        $value = isset($this->xmlData->PurchaseVolume) ? $this->xmlData->PurchaseVolume : false;
        return new OtapiMoney($value);
    }
    /**
     * @return string
     */
    public function GetSkype(){
        $value = isset($this->xmlData->Skype) ? (string)$this->xmlData->Skype : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
}