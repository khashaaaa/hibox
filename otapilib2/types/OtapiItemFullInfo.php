<?php

class OtapiItemFullInfo extends OtapiBaseItemInfo{
    /**
     * @return ArrayOfOtapiDeliveryCost
     */
    public function GetDeliveryCosts(){
        $value = isset($this->xmlData->DeliveryCosts) ? $this->xmlData->DeliveryCosts : false;
        return new ArrayOfOtapiDeliveryCost($value);
    }
    /**
     * @return ArrayOfOtapiItemAttribute
     */
    public function GetAttributes(){
        $value = isset($this->xmlData->Attributes) ? $this->xmlData->Attributes : false;
        return new ArrayOfOtapiItemAttribute($value);
    }
    /**
     * @return boolean
     */
    public function HasHierarchicalConfigurators(){
        $value = isset($this->xmlData->HasHierarchicalConfigurators) ? (string)$this->xmlData->HasHierarchicalConfigurators : false;
        $propertyType = 'boolean';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return ArrayOfOtapiConfiguredItem
     */
    public function GetConfiguredItems(){
        $value = isset($this->xmlData->ConfiguredItems) ? $this->xmlData->ConfiguredItems : false;
        return new ArrayOfOtapiConfiguredItem($value);
    }
    /**
     * @return boolean
     */
    public function IsValidPromotions(){
        $value = isset($this->xmlData->IsValidPromotions) ? (string)$this->xmlData->IsValidPromotions : false;
        $propertyType = 'boolean';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return ArrayOfOtapiItemPromotion
     */
    public function GetPromotions(){
        $value = isset($this->xmlData->Promotions) ? $this->xmlData->Promotions : false;
        return new ArrayOfOtapiItemPromotion($value);
    }
}