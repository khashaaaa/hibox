<?php

class OtapiItemPicture extends BaseOtapiType{
    /**
     * @return string
     */
    public function GetUrl(){
        $value = isset($this->xmlData->Url) ? (string)$this->xmlData->Url : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return OtapiImageUrl
     */
    public function GetSmall(){
        $value = isset($this->xmlData->Small) ? $this->xmlData->Small : false;
        return new OtapiImageUrl($value);
    }
    /**
     * @return OtapiImageUrl
     */
    public function GetMedium(){
        $value = isset($this->xmlData->Medium) ? $this->xmlData->Medium : false;
        return new OtapiImageUrl($value);
    }
    /**
     * @return OtapiImageUrl
     */
    public function GetLarge(){
        $value = isset($this->xmlData->Large) ? $this->xmlData->Large : false;
        return new OtapiImageUrl($value);
    }
    /**
     * @return boolean
     */
    public function IsMain(){
        $value = isset($this->xmlData->IsMain) ? (string)$this->xmlData->IsMain : false;
        $propertyType = 'boolean';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
}