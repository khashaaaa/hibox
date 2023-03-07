<?php

class OtapiProviderInfo extends BaseOtapiType{
    /**
     * @return string
     */
    public function GetType(){
        $value = isset($this->xmlData->Type) ? (string)$this->xmlData->Type : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetAlias(){
        $value = isset($this->xmlData->Alias) ? (string)$this->xmlData->Alias : false;
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
    public function GetCurrencySign(){
        $value = isset($this->xmlData->CurrencySign) ? (string)$this->xmlData->CurrencySign : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetLanguage(){
        $value = isset($this->xmlData->Language) ? (string)$this->xmlData->Language : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetDisplayName(){
        $value = isset($this->xmlData->DisplayName) ? (string)$this->xmlData->DisplayName : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetIconImageUrl(){
        $value = isset($this->xmlData->IconImageUrl) ? (string)$this->xmlData->IconImageUrl : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetRootCategoryId(){
        $value = isset($this->xmlData->RootCategoryId) ? (string)$this->xmlData->RootCategoryId : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return boolean
     */
    public function IsEnabled(){
        $value = isset($this->xmlData->IsEnabled) ? (string)$this->xmlData->IsEnabled : false;
        $propertyType = 'boolean';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return boolean
     */
    public function CanSearchRootCategory(){
        $value = isset($this->xmlData->CanSearchRootCategory) ? (string)$this->xmlData->CanSearchRootCategory : false;
        $propertyType = 'boolean';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetPrefix(){
        $value = isset($this->xmlData->Prefix) ? (string)$this->xmlData->Prefix : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return boolean
     */
    public function HasSettings(){
        $value = isset($this->xmlData->HasSettings) ? (string)$this->xmlData->HasSettings : false;
        $propertyType = 'boolean';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return OtapiArrayOfProviderPromotionTypeInfo
     */
    public function GetPromotionTypes(){
        $value = isset($this->xmlData->PromotionTypes) ? $this->xmlData->PromotionTypes : false;
        return new OtapiArrayOfProviderPromotionTypeInfo($value);
    }
    /**
     * @return boolean
     */
    public function HasInternalDelivery(){
        $value = isset($this->xmlData->HasInternalDelivery) ? (string)$this->xmlData->HasInternalDelivery : false;
        $propertyType = 'boolean';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return boolean
     */
    public function HasItemReviews(){
        $value = isset($this->xmlData->HasItemReviews) ? (string)$this->xmlData->HasItemReviews : false;
        $propertyType = 'boolean';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return OtapiProviderImageSearchInfo
     */
    public function GetImageSearch(){
        $value = isset($this->xmlData->ImageSearch) ? $this->xmlData->ImageSearch : false;
        return new OtapiProviderImageSearchInfo($value);
    }
    /**
     * @return boolean
     */
    public function IsHiddenItemUrls(){
        $value = isset($this->xmlData->IsHiddenItemUrls) ? (string)$this->xmlData->IsHiddenItemUrls : false;
        $propertyType = 'boolean';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return OtapiProviderOrdersIntegrationInfo
     */
    public function GetOrdersIntegration(){
        $value = isset($this->xmlData->OrdersIntegration) ? $this->xmlData->OrdersIntegration : false;
        return new OtapiProviderOrdersIntegrationInfo($value);
    }
    /**
     * @return boolean
     */
    public function RequireInventoryControl(){
        $value = isset($this->xmlData->RequireInventoryControl) ? (string)$this->xmlData->RequireInventoryControl : false;
        $propertyType = 'boolean';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return boolean
     */
    public function IsAllConfigurationsLotQuantity(){
        $value = isset($this->xmlData->IsAllConfigurationsLotQuantity) ? (string)$this->xmlData->IsAllConfigurationsLotQuantity : false;
        $propertyType = 'boolean';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
}