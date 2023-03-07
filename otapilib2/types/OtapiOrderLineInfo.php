<?php

class OtapiOrderLineInfo extends BaseOtapiType{
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
    public function GetOrderId(){
        $value = isset($this->xmlData->OrderId) ? (string)$this->xmlData->OrderId : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetItemTaobaoId(){
        $value = isset($this->xmlData->ItemTaobaoId) ? (string)$this->xmlData->ItemTaobaoId : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetItemId(){
        $value = isset($this->xmlData->ItemId) ? (string)$this->xmlData->ItemId : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetConfigText(){
        $value = isset($this->xmlData->ConfigText) ? (string)$this->xmlData->ConfigText : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetConfigId(){
        $value = isset($this->xmlData->ConfigId) ? (string)$this->xmlData->ConfigId : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return int
     */
    public function GetQty(){
        $value = isset($this->xmlData->Qty) ? (string)$this->xmlData->Qty : false;
        $propertyType = 'int';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return decimal
     */
    public function GetNewPriceCust(){
        $value = isset($this->xmlData->NewPriceCust) ? (string)$this->xmlData->NewPriceCust : false;
        $propertyType = 'decimal';
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
     * @return decimal
     */
    public function GetAmountCust(){
        $value = isset($this->xmlData->AmountCust) ? (string)$this->xmlData->AmountCust : false;
        $propertyType = 'decimal';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetCurrencyCust(){
        $value = isset($this->xmlData->CurrencyCust) ? (string)$this->xmlData->CurrencyCust : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
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
    public function GetAmountInternal(){
        $value = isset($this->xmlData->AmountInternal) ? (string)$this->xmlData->AmountInternal : false;
        $propertyType = 'decimal';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetCurrencyInternal(){
        $value = isset($this->xmlData->CurrencyInternal) ? (string)$this->xmlData->CurrencyInternal : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetInternalPriceCurrencyCode(){
        $value = isset($this->xmlData->InternalPriceCurrencyCode) ? (string)$this->xmlData->InternalPriceCurrencyCode : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return decimal
     */
    public function GetPurchPriceCust(){
        $value = isset($this->xmlData->PurchPriceCust) ? (string)$this->xmlData->PurchPriceCust : false;
        $propertyType = 'decimal';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return decimal
     */
    public function GetPurchDeliveryCust(){
        $value = isset($this->xmlData->PurchDeliveryCust) ? (string)$this->xmlData->PurchDeliveryCust : false;
        $propertyType = 'decimal';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return decimal
     */
    public function GetPurchAmountCust(){
        $value = isset($this->xmlData->PurchAmountCust) ? (string)$this->xmlData->PurchAmountCust : false;
        $propertyType = 'decimal';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return decimal
     */
    public function GetPurchPrice(){
        $value = isset($this->xmlData->PurchPrice) ? (string)$this->xmlData->PurchPrice : false;
        $propertyType = 'decimal';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return decimal
     */
    public function GetPurchDelivery(){
        $value = isset($this->xmlData->PurchDelivery) ? (string)$this->xmlData->PurchDelivery : false;
        $propertyType = 'decimal';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return decimal
     */
    public function GetPurchAmount(){
        $value = isset($this->xmlData->PurchAmount) ? (string)$this->xmlData->PurchAmount : false;
        $propertyType = 'decimal';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetPurchCurrency(){
        $value = isset($this->xmlData->PurchCurrency) ? (string)$this->xmlData->PurchCurrency : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetBriefDescrTrans(){
        $value = isset($this->xmlData->BriefDescrTrans) ? (string)$this->xmlData->BriefDescrTrans : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetItemImageURL(){
        $value = isset($this->xmlData->ItemImageURL) ? (string)$this->xmlData->ItemImageURL : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetItemExternalURL(){
        $value = isset($this->xmlData->ItemExternalURL) ? (string)$this->xmlData->ItemExternalURL : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetVendNick(){
        $value = isset($this->xmlData->VendNick) ? (string)$this->xmlData->VendNick : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetVendId(){
        $value = isset($this->xmlData->VendId) ? (string)$this->xmlData->VendId : false;
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
    public function GetOperatorComment(){
        $value = isset($this->xmlData->OperatorComment) ? (string)$this->xmlData->OperatorComment : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return short
     */
    public function GetStatusCode(){
        $value = isset($this->xmlData->StatusCode) ? (string)$this->xmlData->StatusCode : false;
        $propertyType = 'short';
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
    public function GetSubSalesNum(){
        $value = isset($this->xmlData->SubSalesNum) ? (string)$this->xmlData->SubSalesNum : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetSubSalesDate(){
        $value = isset($this->xmlData->SubSalesDate) ? (string)$this->xmlData->SubSalesDate : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetSubSalesTime(){
        $value = isset($this->xmlData->SubSalesTime) ? (string)$this->xmlData->SubSalesTime : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetVendPurchId(){
        $value = isset($this->xmlData->VendPurchId) ? (string)$this->xmlData->VendPurchId : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetVendPurchWaybill(){
        $value = isset($this->xmlData->VendPurchWaybill) ? (string)$this->xmlData->VendPurchWaybill : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return decimal
     */
    public function GetTaoBaoDelivery(){
        $value = isset($this->xmlData->TaoBaoDelivery) ? (string)$this->xmlData->TaoBaoDelivery : false;
        $propertyType = 'decimal';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
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
    /**
     * @return decimal
     */
    public function GetInternalDeliveryPrice(){
        $value = isset($this->xmlData->InternalDeliveryPrice) ? (string)$this->xmlData->InternalDeliveryPrice : false;
        $propertyType = 'decimal';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetPromotionId(){
        $value = isset($this->xmlData->PromotionId) ? (string)$this->xmlData->PromotionId : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetPriceWithoutDelivery(){
        $value = isset($this->xmlData->PriceWithoutDelivery) ? (string)$this->xmlData->PriceWithoutDelivery : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetCategoryId(){
        $value = isset($this->xmlData->CategoryId) ? (string)$this->xmlData->CategoryId : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetVendURL(){
        $value = isset($this->xmlData->VendURL) ? (string)$this->xmlData->VendURL : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetQtyOrig(){
        $value = isset($this->xmlData->QtyOrig) ? (string)$this->xmlData->QtyOrig : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return int
     */
    public function GetLineNum(){
        $value = isset($this->xmlData->LineNum) ? (string)$this->xmlData->LineNum : false;
        $propertyType = 'int';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return OtapiArrayOfSalesLineStatusInfo
     */
    public function GetAvailableStatusList(){
        $value = isset($this->xmlData->AvailableStatusList) ? $this->xmlData->AvailableStatusList : false;
        return new OtapiArrayOfSalesLineStatusInfo($value);
    }
    /**
     * @return string
     */
    public function GetNameOrig(){
        $value = isset($this->xmlData->NameOrig) ? (string)$this->xmlData->NameOrig : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetConfigExternalTextOrig(){
        $value = isset($this->xmlData->ConfigExternalTextOrig) ? (string)$this->xmlData->ConfigExternalTextOrig : false;
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
     * @return string
     */
    public function GetOrderedDateTime(){
        $value = isset($this->xmlData->OrderedDateTime) ? (string)$this->xmlData->OrderedDateTime : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetCustomDescription(){
        $value = isset($this->xmlData->CustomDescription) ? (string)$this->xmlData->CustomDescription : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetCustomDescriptionInEnglish(){
        $value = isset($this->xmlData->CustomDescriptionInEnglish) ? (string)$this->xmlData->CustomDescriptionInEnglish : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return boolean
     */
    public function CanMoveToPackage(){
        $value = isset($this->xmlData->CanMoveToPackage) ? (string)$this->xmlData->CanMoveToPackage : false;
        $propertyType = 'boolean';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return boolean
     */
    public function CanBePurchased(){
        $value = isset($this->xmlData->CanBePurchased) ? (string)$this->xmlData->CanBePurchased : false;
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
    public function GetInternalDeliveryType(){
        $value = isset($this->xmlData->InternalDeliveryType) ? (string)$this->xmlData->InternalDeliveryType : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
}