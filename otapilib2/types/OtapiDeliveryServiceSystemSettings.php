<?php

class OtapiDeliveryServiceSystemSettings extends BaseOtapiType{
    /**
     * @return OtapiMetaInfo
     */
    public function GetMetaInfo(){
        $value = isset($this->xmlData->MetaInfo) ? $this->xmlData->MetaInfo : false;
        return new OtapiMetaInfo($value);
    }
    /**
     * @return boolean
     */
    public function IsAvailable(){
        $value = isset($this->xmlData->IsAvailable) ? (string)$this->xmlData->IsAvailable : false;
        $propertyType = 'boolean';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetBoxberryToken(){
        $value = isset($this->xmlData->BoxberryToken) ? (string)$this->xmlData->BoxberryToken : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetBoxberrySendPickupPointCode(){
        $value = isset($this->xmlData->BoxberrySendPickupPointCode) ? (string)$this->xmlData->BoxberrySendPickupPointCode : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
}