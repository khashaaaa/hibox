<?php

class OtapiMessengerSettings extends BaseOtapiType{
    /**
     * @return OtapiMetaInfo
     */
    public function GetMetaInfo(){
        $value = isset($this->xmlData->MetaInfo) ? $this->xmlData->MetaInfo : false;
        return new OtapiMetaInfo($value);
    }
    /**
     * @return OtapiValueListOfString
     */
    public function GetNotificationEmails(){
        $value = isset($this->xmlData->NotificationEmails) ? $this->xmlData->NotificationEmails : false;
        return new OtapiValueListOfString($value);
    }
    /**
     * @return string
     */
    public function GetNotificationLanguage(){
        $value = isset($this->xmlData->NotificationLanguage) ? (string)$this->xmlData->NotificationLanguage : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return OtapiValueListOfEventType
     */
    public function GetActiveNotifications(){
        $value = isset($this->xmlData->ActiveNotifications) ? $this->xmlData->ActiveNotifications : false;
        return new OtapiValueListOfEventType($value);
    }
}