<?php

class OtapiSocialNetworkInfo extends BaseOtapiType{
    /**
     * @return string
     */
    public function GetId(){
        $value = isset($this->xmlData->Id) ? (string)$this->xmlData->Id : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetName(){
        $value = isset($this->xmlData->Name) ? (string)$this->xmlData->Name : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetLink(){
        $value = isset($this->xmlData->Link) ? (string)$this->xmlData->Link : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetLikeWidget(){
        $value = isset($this->xmlData->LikeWidget) ? (string)$this->xmlData->LikeWidget : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetShareWidget(){
        $value = isset($this->xmlData->ShareWidget) ? (string)$this->xmlData->ShareWidget : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetSubscribeWidget(){
        $value = isset($this->xmlData->SubscribeWidget) ? (string)$this->xmlData->SubscribeWidget : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetIconUrl(){
        $value = isset($this->xmlData->IconUrl) ? (string)$this->xmlData->IconUrl : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
}