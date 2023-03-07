<?php

class OtapiTicketMessage extends BaseOtapiType{
    /**
     * @return string
     */
    public function GetMessageId(){
        $value = isset($this->xmlData->MessageId) ? (string)$this->xmlData->MessageId : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetCreatedDate(){
        $value = isset($this->xmlData->CreatedDate) ? (string)$this->xmlData->CreatedDate : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetCreatedTime(){
        $value = isset($this->xmlData->CreatedTime) ? (string)$this->xmlData->CreatedTime : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetText(){
        $value = isset($this->xmlData->Text) ? (string)$this->xmlData->Text : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetDirection(){
        $value = isset($this->xmlData->Direction) ? (string)$this->xmlData->Direction : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
}