<?php

class OtapiPromoItemInfo extends OtapiBaseItemInfo{
    /**
     * @return string
     */
    public function GetClickUrl(){
        $value = isset($this->xmlData->ClickUrl) ? (string)$this->xmlData->ClickUrl : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetKeywordClickUrl(){
        $value = isset($this->xmlData->KeywordClickUrl) ? (string)$this->xmlData->KeywordClickUrl : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetShopClickUrl(){
        $value = isset($this->xmlData->ShopClickUrl) ? (string)$this->xmlData->ShopClickUrl : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetTaobaokeCatClickUrl(){
        $value = isset($this->xmlData->TaobaokeCatClickUrl) ? (string)$this->xmlData->TaobaokeCatClickUrl : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetCommission(){
        $value = isset($this->xmlData->Commission) ? (string)$this->xmlData->Commission : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetCommissionNum(){
        $value = isset($this->xmlData->CommissionNum) ? (string)$this->xmlData->CommissionNum : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetCommissionRate(){
        $value = isset($this->xmlData->CommissionRate) ? (string)$this->xmlData->CommissionRate : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetCommissionVolume(){
        $value = isset($this->xmlData->CommissionVolume) ? (string)$this->xmlData->CommissionVolume : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetCouponEndTime(){
        $value = isset($this->xmlData->CouponEndTime) ? (string)$this->xmlData->CouponEndTime : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetCouponPrice(){
        $value = isset($this->xmlData->CouponPrice) ? (string)$this->xmlData->CouponPrice : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetCouponRate(){
        $value = isset($this->xmlData->CouponRate) ? (string)$this->xmlData->CouponRate : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetCouponStartTime(){
        $value = isset($this->xmlData->CouponStartTime) ? (string)$this->xmlData->CouponStartTime : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetShopType(){
        $value = isset($this->xmlData->ShopType) ? (string)$this->xmlData->ShopType : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
}