<?php

class OtapiGetEditableCategorySubcategories extends BaseOtapiType{
    /**
     * @return string
     */
    public function GetInstanceKey(){
        $value = isset($this->xmlData->instanceKey) ? (string)$this->xmlData->instanceKey : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetLanguage(){
        $value = isset($this->xmlData->language) ? (string)$this->xmlData->language : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetSessionId(){
        $value = isset($this->xmlData->sessionId) ? (string)$this->xmlData->sessionId : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetParentCategoryId(){
        $value = isset($this->xmlData->parentCategoryId) ? (string)$this->xmlData->parentCategoryId : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return boolean
     */
    public function NeedHighlightParentsOfDeletedCategories(){
        $value = isset($this->xmlData->needHighlightParentsOfDeletedCategories) ? (string)$this->xmlData->needHighlightParentsOfDeletedCategories : false;
        $propertyType = 'boolean';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
}