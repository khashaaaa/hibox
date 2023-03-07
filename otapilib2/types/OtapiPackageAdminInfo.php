<?php

class OtapiPackageAdminInfo extends BaseOtapiType{
    /**
     * @return decimal
     */
    public function GetPriceInternal(){
        $value = isset($this->xmlData->PriceInternal) ? (string)$this->xmlData->PriceInternal : false;
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
    public function GetPriceCurrencyCode(){
        $value = isset($this->xmlData->PriceCurrencyCode) ? (string)$this->xmlData->PriceCurrencyCode : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return dateTime
     */
    public function GetPriceUpdateDate(){
        $value = isset($this->xmlData->PriceUpdateDate) ? (string)$this->xmlData->PriceUpdateDate : false;
        $propertyType = 'dateTime';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetDeliveryTrackingNum(){
        $value = isset($this->xmlData->DeliveryTrackingNum) ? (string)$this->xmlData->DeliveryTrackingNum : false;
        $propertyType = 'string';
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
     * @return int
     */
    public function GetManualPrice(){
        $value = isset($this->xmlData->ManualPrice) ? (string)$this->xmlData->ManualPrice : false;
        $propertyType = 'int';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetDeliveryModeId(){
        $value = isset($this->xmlData->DeliveryModeId) ? (string)$this->xmlData->DeliveryModeId : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetDeliveryContactLastname(){
        $value = isset($this->xmlData->DeliveryContactLastname) ? (string)$this->xmlData->DeliveryContactLastname : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetDeliveryContactFirstname(){
        $value = isset($this->xmlData->DeliveryContactFirstname) ? (string)$this->xmlData->DeliveryContactFirstname : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetDeliveryContactMiddlename(){
        $value = isset($this->xmlData->DeliveryContactMiddlename) ? (string)$this->xmlData->DeliveryContactMiddlename : false;
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
     * @return string
     */
    public function GetDeliveryCountry(){
        $value = isset($this->xmlData->DeliveryCountry) ? (string)$this->xmlData->DeliveryCountry : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetDeliveryPostalCode(){
        $value = isset($this->xmlData->DeliveryPostalCode) ? (string)$this->xmlData->DeliveryPostalCode : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetDeliveryRegionName(){
        $value = isset($this->xmlData->DeliveryRegionName) ? (string)$this->xmlData->DeliveryRegionName : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetDeliveryCity(){
        $value = isset($this->xmlData->DeliveryCity) ? (string)$this->xmlData->DeliveryCity : false;
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
     * @return string
     */
    public function GetId(){
        $value = isset($this->xmlData->Id) ? (string)$this->xmlData->Id : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return OtapiOrderId
     */
    public function GetOrderId(){
        $value = isset($this->xmlData->OrderId) ? $this->xmlData->OrderId : false;
        return new OtapiOrderId($value);
    }
    /**
     * @return string
     */
    public function GetStatusId(){
        $value = isset($this->xmlData->StatusId) ? (string)$this->xmlData->StatusId : false;
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
    public function GetCurrencyCodeInternal(){
        $value = isset($this->xmlData->CurrencyCodeInternal) ? (string)$this->xmlData->CurrencyCodeInternal : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetCurrencySignInternal(){
        $value = isset($this->xmlData->CurrencySignInternal) ? (string)$this->xmlData->CurrencySignInternal : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return decimal
     */
    public function GetPriceCust(){
        $value = isset($this->xmlData->PriceCust) ? (string)$this->xmlData->PriceCust : false;
        $propertyType = 'decimal';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetCurrencyCodeCust(){
        $value = isset($this->xmlData->CurrencyCodeCust) ? (string)$this->xmlData->CurrencyCodeCust : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetCurrencySignCust(){
        $value = isset($this->xmlData->CurrencySignCust) ? (string)$this->xmlData->CurrencySignCust : false;
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
     * @return int
     */
    public function GetCanDelete(){
        $value = isset($this->xmlData->CanDelete) ? (string)$this->xmlData->CanDelete : false;
        $propertyType = 'int';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return int
     */
    public function GetCanUpdate(){
        $value = isset($this->xmlData->CanUpdate) ? (string)$this->xmlData->CanUpdate : false;
        $propertyType = 'int';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return boolean
     */
    public function CanChangeStatus(){
        $value = isset($this->xmlData->CanChangeStatus) ? (string)$this->xmlData->CanChangeStatus : false;
        $propertyType = 'boolean';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return boolean
     */
    public function CanPrintPackageReceipt(){
        $value = isset($this->xmlData->CanPrintPackageReceipt) ? (string)$this->xmlData->CanPrintPackageReceipt : false;
        $propertyType = 'boolean';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return boolean
     */
    public function CanExportToExternalDeliverySystem(){
        $value = isset($this->xmlData->CanExportToExternalDeliverySystem) ? (string)$this->xmlData->CanExportToExternalDeliverySystem : false;
        $propertyType = 'boolean';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return dateTime
     */
    public function GetShipmentDate(){
        $value = isset($this->xmlData->ShipmentDate) ? (string)$this->xmlData->ShipmentDate : false;
        $propertyType = 'dateTime';
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
     * @return OtapiArrayOfPackageItem
     */
    public function GetItems(){
        $value = isset($this->xmlData->Items) ? $this->xmlData->Items : false;
        return new OtapiArrayOfPackageItem($value);
    }
    /**
     * @return string
     */
    public function GetDeliveryCountryCode(){
        $value = isset($this->xmlData->DeliveryCountryCode) ? (string)$this->xmlData->DeliveryCountryCode : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
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
    public function GetExternalPackageId(){
        $value = isset($this->xmlData->ExternalPackageId) ? (string)$this->xmlData->ExternalPackageId : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
}