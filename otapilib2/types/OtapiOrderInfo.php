<?php

class OtapiOrderInfo extends BaseOtapiType{
    /**
     * @return OtapiOrderTHSInfo
     */
    public function GetTHSInfo(){
        $value = isset($this->xmlData->THSInfo) ? $this->xmlData->THSInfo : false;
        return new OtapiOrderTHSInfo($value);
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
    public function GetSubstatusCode(){
        $value = isset($this->xmlData->SubstatusCode) ? (string)$this->xmlData->SubstatusCode : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetSubstatusName(){
        $value = isset($this->xmlData->SubstatusName) ? (string)$this->xmlData->SubstatusName : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetOperatorId(){
        $value = isset($this->xmlData->OperatorId) ? (string)$this->xmlData->OperatorId : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetOperatorName(){
        $value = isset($this->xmlData->OperatorName) ? (string)$this->xmlData->OperatorName : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return long
     */
    public function GetItemsCount(){
        $value = isset($this->xmlData->ItemsCount) ? (string)$this->xmlData->ItemsCount : false;
        $propertyType = 'long';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return decimal
     */
    public function GetGoodsAmount(){
        $value = isset($this->xmlData->GoodsAmount) ? (string)$this->xmlData->GoodsAmount : false;
        $propertyType = 'decimal';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return decimal
     */
    public function GetDeliveryAmount(){
        $value = isset($this->xmlData->DeliveryAmount) ? (string)$this->xmlData->DeliveryAmount : false;
        $propertyType = 'decimal';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return decimal
     */
    public function GetLogisticianDeliveryPrice(){
        $value = isset($this->xmlData->LogisticianDeliveryPrice) ? (string)$this->xmlData->LogisticianDeliveryPrice : false;
        $propertyType = 'decimal';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return decimal
     */
    public function GetTotalAmount(){
        $value = isset($this->xmlData->TotalAmount) ? (string)$this->xmlData->TotalAmount : false;
        $propertyType = 'decimal';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return decimal
     */
    public function GetRemainAmount(){
        $value = isset($this->xmlData->RemainAmount) ? (string)$this->xmlData->RemainAmount : false;
        $propertyType = 'decimal';
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
    public function GetCurrencyCode(){
        $value = isset($this->xmlData->CurrencyCode) ? (string)$this->xmlData->CurrencyCode : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetCustComment(){
        $value = isset($this->xmlData->CustComment) ? (string)$this->xmlData->CustComment : false;
        $propertyType = 'string';
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
    public function GetDeliveryModeName(){
        $value = isset($this->xmlData->DeliveryModeName) ? (string)$this->xmlData->DeliveryModeName : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return int
     */
    public function GetCanCancel(){
        $value = isset($this->xmlData->CanCancel) ? (string)$this->xmlData->CanCancel : false;
        $propertyType = 'int';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return int
     */
    public function GetCanConfirmShipment(){
        $value = isset($this->xmlData->CanConfirmShipment) ? (string)$this->xmlData->CanConfirmShipment : false;
        $propertyType = 'int';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return int
     */
    public function GetCanChangeAddress(){
        $value = isset($this->xmlData->CanChangeAddress) ? (string)$this->xmlData->CanChangeAddress : false;
        $propertyType = 'int';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return int
     */
    public function GetCanRestore(){
        $value = isset($this->xmlData->CanRestore) ? (string)$this->xmlData->CanRestore : false;
        $propertyType = 'int';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return int
     */
    public function GetCanClose(){
        $value = isset($this->xmlData->CanClose) ? (string)$this->xmlData->CanClose : false;
        $propertyType = 'int';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return int
     */
    public function GetCanCloseCancel(){
        $value = isset($this->xmlData->CanCloseCancel) ? (string)$this->xmlData->CanCloseCancel : false;
        $propertyType = 'int';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return int
     */
    public function GetCanAccept(){
        $value = isset($this->xmlData->CanAccept) ? (string)$this->xmlData->CanAccept : false;
        $propertyType = 'int';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return int
     */
    public function GetCanPurchaseItems(){
        $value = isset($this->xmlData->CanPurchaseItems) ? (string)$this->xmlData->CanPurchaseItems : false;
        $propertyType = 'int';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return int
     */
    public function GetEstimatedWeight(){
        $value = isset($this->xmlData->EstimatedWeight) ? (string)$this->xmlData->EstimatedWeight : false;
        $propertyType = 'int';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetCustId(){
        $value = isset($this->xmlData->CustId) ? (string)$this->xmlData->CustId : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetCustName(){
        $value = isset($this->xmlData->CustName) ? (string)$this->xmlData->CustName : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return int
     */
    public function GetStatusId(){
        $value = isset($this->xmlData->StatusId) ? (string)$this->xmlData->StatusId : false;
        $propertyType = 'int';
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
     * @return OtapiDeliveryAddress
     */
    public function GetDeliveryAddress(){
        $value = isset($this->xmlData->DeliveryAddress) ? $this->xmlData->DeliveryAddress : false;
        return new OtapiDeliveryAddress($value);
    }
    /**
     * @return decimal
     */
    public function GetTaoBaoPrice(){
        $value = isset($this->xmlData->TaoBaoPrice) ? (string)$this->xmlData->TaoBaoPrice : false;
        $propertyType = 'decimal';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return decimal
     */
    public function GetTaoBaoPriceWithDiscount(){
        $value = isset($this->xmlData->TaoBaoPriceWithDiscount) ? (string)$this->xmlData->TaoBaoPriceWithDiscount : false;
        $propertyType = 'decimal';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return decimal
     */
    public function GetInternalDeliveryOriginalInExternalCurrency(){
        $value = isset($this->xmlData->InternalDeliveryOriginalInExternalCurrency) ? (string)$this->xmlData->InternalDeliveryOriginalInExternalCurrency : false;
        $propertyType = 'decimal';
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
     * @return string
     */
    public function GetExternalCurrencyCode(){
        $value = isset($this->xmlData->ExternalCurrencyCode) ? (string)$this->xmlData->ExternalCurrencyCode : false;
        $propertyType = 'string';
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
     * @return OtapiArrayOfOrderLineStatusSummary
     */
    public function GetLineStatusSummaries(){
        $value = isset($this->xmlData->LineStatusSummaries) ? $this->xmlData->LineStatusSummaries : false;
        return new OtapiArrayOfOrderLineStatusSummary($value);
    }
    /**
     * @return decimal
     */
    public function GetUserAccountAvailableAmount(){
        $value = isset($this->xmlData->UserAccountAvailableAmount) ? (string)$this->xmlData->UserAccountAvailableAmount : false;
        $propertyType = 'decimal';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetProviderTypeEnum(){
        $value = isset($this->xmlData->ProviderTypeEnum) ? (string)$this->xmlData->ProviderTypeEnum : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return boolean
     */
    public function IsTest(){
        $value = isset($this->xmlData->IsTest) ? (string)$this->xmlData->IsTest : false;
        $propertyType = 'boolean';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return boolean
     */
    public function IsPaid(){
        $value = isset($this->xmlData->IsPaid) ? (string)$this->xmlData->IsPaid : false;
        $propertyType = 'boolean';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return boolean
     */
    public function IsCancelled(){
        $value = isset($this->xmlData->IsCancelled) ? (string)$this->xmlData->IsCancelled : false;
        $propertyType = 'boolean';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return boolean
     */
    public function IsCompleted(){
        $value = isset($this->xmlData->IsCompleted) ? (string)$this->xmlData->IsCompleted : false;
        $propertyType = 'boolean';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetCreatedDateTime(){
        $value = isset($this->xmlData->CreatedDateTime) ? (string)$this->xmlData->CreatedDateTime : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return decimal
     */
    public function GetInternalOriginalPrice(){
        $value = isset($this->xmlData->InternalOriginalPrice) ? (string)$this->xmlData->InternalOriginalPrice : false;
        $propertyType = 'decimal';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return decimal
     */
    public function GetInternalOriginalPriceWithDiscount(){
        $value = isset($this->xmlData->InternalOriginalPriceWithDiscount) ? (string)$this->xmlData->InternalOriginalPriceWithDiscount : false;
        $propertyType = 'decimal';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
}