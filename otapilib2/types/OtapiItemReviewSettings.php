<?php

class OtapiItemReviewSettings extends BaseOtapiType{
    /**
     * @return OtapiMetaInfo
     */
    public function GetMetaInfo(){
        $value = isset($this->xmlData->MetaInfo) ? $this->xmlData->MetaInfo : false;
        return new OtapiMetaInfo($value);
    }
    /**
     * @return string
     */
    public function GetVersion(){
        $value = isset($this->xmlData->Version) ? (string)$this->xmlData->Version : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return int
     */
    public function GetReviewCount(){
        $value = isset($this->xmlData->ReviewCount) ? (string)$this->xmlData->ReviewCount : false;
        $propertyType = 'int';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return int
     */
    public function GetWholePlatformReviewCount(){
        $value = isset($this->xmlData->WholePlatformReviewCount) ? (string)$this->xmlData->WholePlatformReviewCount : false;
        $propertyType = 'int';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return boolean
     */
    public function ShowWholePlatformReviews(){
        $value = isset($this->xmlData->ShowWholePlatformReviews) ? (string)$this->xmlData->ShowWholePlatformReviews : false;
        $propertyType = 'boolean';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
}