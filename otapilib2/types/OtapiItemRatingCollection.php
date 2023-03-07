<?php

class OtapiItemRatingCollection extends BaseOtapiType{
    /**
     * @return string
     */
    public function GetContentTypeName(){
        $value = isset($this->xmlData->ContentTypeName) ? (string)$this->xmlData->ContentTypeName : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetItemRatingTypeName(){
        $value = isset($this->xmlData->ItemRatingTypeName) ? (string)$this->xmlData->ItemRatingTypeName : false;
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
     * @return int
     */
    public function GetCount(){
        $value = isset($this->xmlData->Count) ? (string)$this->xmlData->Count : false;
        $propertyType = 'int';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
}