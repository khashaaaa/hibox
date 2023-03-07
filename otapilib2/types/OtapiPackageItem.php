<?php

class OtapiPackageItem extends BaseOtapiType{
    /**
     * @return string
     */
    public function GetOrderLineId(){
        $value = isset($this->xmlData->OrderLineId) ? (string)$this->xmlData->OrderLineId : false;
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
     * @return int
     */
    public function GetQuantity(){
        $value = isset($this->xmlData->Quantity) ? (string)$this->xmlData->Quantity : false;
        $propertyType = 'int';
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
     * @return OtapiMoney
     */
    public function GetOneItemPriceInternal(){
        $value = isset($this->xmlData->OneItemPriceInternal) ? $this->xmlData->OneItemPriceInternal : false;
        return new OtapiMoney($value);
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
     * @return OtapiMoney
     */
    public function GetTotalPriceInternal(){
        $value = isset($this->xmlData->TotalPriceInternal) ? $this->xmlData->TotalPriceInternal : false;
        return new OtapiMoney($value);
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
    public function GetWeightNet(){
        $value = isset($this->xmlData->WeightNet) ? (string)$this->xmlData->WeightNet : false;
        $propertyType = 'decimal';
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
    public function GetItemURL(){
        $value = isset($this->xmlData->ItemURL) ? (string)$this->xmlData->ItemURL : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
}