<?php

class OtapiSocialNetworkData extends OtapiAbstractMetaListItem{
    /**
     * @return string
     */
    public function GetWarning(){
        $value = isset($this->xmlData->Warning) ? (string)$this->xmlData->Warning : false;
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
     * @return OtapiValueListOfFileInfo
     */
    public function GetIconUrl(){
        $value = isset($this->xmlData->IconUrl) ? $this->xmlData->IconUrl : false;
        return new OtapiValueListOfFileInfo($value);
    }
}