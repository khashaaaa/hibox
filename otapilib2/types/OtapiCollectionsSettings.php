<?php

class OtapiCollectionsSettings extends BaseOtapiType{
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
    public function IsAbandonedCartNotificationEnabled(){
        $value = isset($this->xmlData->IsAbandonedCartNotificationEnabled) ? (string)$this->xmlData->IsAbandonedCartNotificationEnabled : false;
        $propertyType = 'boolean';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return boolean
     */
    public function IsCartReadyForClearanceNotificationEnabled(){
        $value = isset($this->xmlData->IsCartReadyForClearanceNotificationEnabled) ? (string)$this->xmlData->IsCartReadyForClearanceNotificationEnabled : false;
        $propertyType = 'boolean';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return int
     */
    public function GetAbandonedCartNotificationDelay(){
        $value = isset($this->xmlData->AbandonedCartNotificationDelay) ? (string)$this->xmlData->AbandonedCartNotificationDelay : false;
        $propertyType = 'int';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return int
     */
    public function GetCartReadyForClearanceNotificationDelay(){
        $value = isset($this->xmlData->CartReadyForClearanceNotificationDelay) ? (string)$this->xmlData->CartReadyForClearanceNotificationDelay : false;
        $propertyType = 'int';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return int
     */
    public function GetDelayBeforeCartClearance(){
        $value = isset($this->xmlData->DelayBeforeCartClearance) ? (string)$this->xmlData->DelayBeforeCartClearance : false;
        $propertyType = 'int';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
}