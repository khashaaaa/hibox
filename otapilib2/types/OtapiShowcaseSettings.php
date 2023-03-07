<?php

class OtapiShowcaseSettings extends BaseOtapiType{
    /**
     * @return OtapiCurrencyInformation
     */
    public function GetRuble(){
        $value = isset($this->xmlData->Ruble) ? $this->xmlData->Ruble : false;
        return new OtapiCurrencyInformation($value);
    }
    /**
     * @return OtapiCurrencyInformation
     */
    public function GetDollar(){
        $value = isset($this->xmlData->Dollar) ? $this->xmlData->Dollar : false;
        return new OtapiCurrencyInformation($value);
    }
    /**
     * @return OtapiCurrencyInformation
     */
    public function GetYuan(){
        $value = isset($this->xmlData->Yuan) ? $this->xmlData->Yuan : false;
        return new OtapiCurrencyInformation($value);
    }
    /**
     * @return decimal
     */
    public function GetMarginPercentage(){
        $value = isset($this->xmlData->MarginPercentage) ? (string)$this->xmlData->MarginPercentage : false;
        $propertyType = 'decimal';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return decimal
     */
    public function GetMinimumMargin(){
        $value = isset($this->xmlData->MinimumMargin) ? (string)$this->xmlData->MinimumMargin : false;
        $propertyType = 'decimal';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return boolean
     */
    public function IsSinchroCB(){
        $value = isset($this->xmlData->IsSinchroCB) ? (string)$this->xmlData->IsSinchroCB : false;
        $propertyType = 'boolean';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return OtapiArrayOfDeliveryTypes
     */
    public function GetDeliveryTypes(){
        $value = isset($this->xmlData->DeliveryTypes) ? $this->xmlData->DeliveryTypes : false;
        return new OtapiArrayOfDeliveryTypes($value);
    }
    /**
     * @return OtapiArrayOfNamedProperty
     */
    public function GetAvailableDeliveryTypes(){
        $value = isset($this->xmlData->AvailableDeliveryTypes) ? $this->xmlData->AvailableDeliveryTypes : false;
        return new OtapiArrayOfNamedProperty($value);
    }
    /**
     * @return string
     */
    public function GetExternalDeliveryRegionId(){
        $value = isset($this->xmlData->ExternalDeliveryRegionId) ? (string)$this->xmlData->ExternalDeliveryRegionId : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetExternalDeliveryRegionName(){
        $value = isset($this->xmlData->ExternalDeliveryRegionName) ? (string)$this->xmlData->ExternalDeliveryRegionName : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return boolean
     */
    public function IsAuctionTypeItemSellAllowed(){
        $value = isset($this->xmlData->IsAuctionTypeItemSellAllowed) ? (string)$this->xmlData->IsAuctionTypeItemSellAllowed : false;
        $propertyType = 'boolean';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return boolean
     */
    public function IsNotDeliverableItemSellAllowed(){
        $value = isset($this->xmlData->IsNotDeliverableItemSellAllowed) ? (string)$this->xmlData->IsNotDeliverableItemSellAllowed : false;
        $propertyType = 'boolean';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return boolean
     */
    public function IsSecondhandItemSellAllowed(){
        $value = isset($this->xmlData->IsSecondhandItemSellAllowed) ? (string)$this->xmlData->IsSecondhandItemSellAllowed : false;
        $propertyType = 'boolean';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return boolean
     */
    public function IsFilteredItemsSellAllowed(){
        $value = isset($this->xmlData->IsFilteredItemsSellAllowed) ? (string)$this->xmlData->IsFilteredItemsSellAllowed : false;
        $propertyType = 'boolean';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return boolean
     */
    public function IsInStockItemSellAllowed(){
        $value = isset($this->xmlData->IsInStockItemSellAllowed) ? (string)$this->xmlData->IsInStockItemSellAllowed : false;
        $propertyType = 'boolean';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return boolean
     */
    public function IsNotSelectorSellAllowed(){
        $value = isset($this->xmlData->IsNotSelectorSellAllowed) ? (string)$this->xmlData->IsNotSelectorSellAllowed : false;
        $propertyType = 'boolean';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return boolean
     */
    public function UseDiscount(){
        $value = isset($this->xmlData->UseDiscount) ? (string)$this->xmlData->UseDiscount : false;
        $propertyType = 'boolean';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetDateTimeFormat(){
        $value = isset($this->xmlData->DateTimeFormat) ? (string)$this->xmlData->DateTimeFormat : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return boolean
     */
    public function UseVipDiscount(){
        $value = isset($this->xmlData->UseVipDiscount) ? (string)$this->xmlData->UseVipDiscount : false;
        $propertyType = 'boolean';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetDiscountMode(){
        $value = isset($this->xmlData->DiscountMode) ? (string)$this->xmlData->DiscountMode : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return OtapiArrayOfNamedProperty
     */
    public function GetAvailableDiscountModes(){
        $value = isset($this->xmlData->AvailableDiscountModes) ? $this->xmlData->AvailableDiscountModes : false;
        return new OtapiArrayOfNamedProperty($value);
    }
    /**
     * @return boolean
     */
    public function LimitItemsByCatalog(){
        $value = isset($this->xmlData->LimitItemsByCatalog) ? (string)$this->xmlData->LimitItemsByCatalog : false;
        $propertyType = 'boolean';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
}