<?php

class OtapiSmsServiceModeSettings extends OtapiAbstractMetaListItem{
    /**
     * @return string
     */
    public function GetSmsService(){
        $value = isset($this->xmlData->SmsService) ? (string)$this->xmlData->SmsService : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetAccount(){
        $value = isset($this->xmlData->Account) ? (string)$this->xmlData->Account : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetSecret(){
        $value = isset($this->xmlData->Secret) ? (string)$this->xmlData->Secret : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetSender(){
        $value = isset($this->xmlData->Sender) ? (string)$this->xmlData->Sender : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetApiId(){
        $value = isset($this->xmlData->ApiId) ? (string)$this->xmlData->ApiId : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return boolean
     */
    public function IsEnabled(){
        $value = isset($this->xmlData->IsEnabled) ? (string)$this->xmlData->IsEnabled : false;
        $propertyType = 'boolean';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetTestRecipientPhone(){
        $value = isset($this->xmlData->TestRecipientPhone) ? (string)$this->xmlData->TestRecipientPhone : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
}