<?php

class OtapiOrderSettings extends BaseOtapiType{
    /**
     * @return OtapiMetaInfo
     */
    public function GetMetaInfo(){
        $value = isset($this->xmlData->MetaInfo) ? $this->xmlData->MetaInfo : false;
        return new OtapiMetaInfo($value);
    }
    /**
     * @return decimal
     */
    public function GetMinOrderCost(){
        $value = isset($this->xmlData->MinOrderCost) ? (string)$this->xmlData->MinOrderCost : false;
        $propertyType = 'decimal';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return short
     */
    public function GetMaxNoteItemsCount(){
        $value = isset($this->xmlData->MaxNoteItemsCount) ? (string)$this->xmlData->MaxNoteItemsCount : false;
        $propertyType = 'short';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return short
     */
    public function GetMaxCartItemsCount(){
        $value = isset($this->xmlData->MaxCartItemsCount) ? (string)$this->xmlData->MaxCartItemsCount : false;
        $propertyType = 'short';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetOrderPrefix(){
        $value = isset($this->xmlData->OrderPrefix) ? (string)$this->xmlData->OrderPrefix : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
}