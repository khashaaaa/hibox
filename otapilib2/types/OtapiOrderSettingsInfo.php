<?php

class OtapiOrderSettingsInfo extends BaseOtapiType{
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
}