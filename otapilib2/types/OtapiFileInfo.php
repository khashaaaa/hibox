<?php

class OtapiFileInfo extends BaseOtapiType{
    /**
     * @return string
     */
    public function GetFileType(){
        $value = isset($this->xmlData->FileType) ? (string)$this->xmlData->FileType : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetStatus(){
        $value = isset($this->xmlData->Status) ? (string)$this->xmlData->Status : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetStatusDescription(){
        $value = isset($this->xmlData->StatusDescription) ? (string)$this->xmlData->StatusDescription : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetUrl(){
        $value = isset($this->xmlData->Url) ? (string)$this->xmlData->Url : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return int
     */
    public function GetSize(){
        $value = isset($this->xmlData->Size) ? (string)$this->xmlData->Size : false;
        $propertyType = 'int';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return dateTime
     */
    public function GetUpdatedTime(){
        $value = isset($this->xmlData->UpdatedTime) ? (string)$this->xmlData->UpdatedTime : false;
        $propertyType = 'dateTime';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetPreviewUrl(){
        $value = isset($this->xmlData->PreviewUrl) ? (string)$this->xmlData->PreviewUrl : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return int
     */
    public function GetImageWidth(){
        $value = isset($this->xmlData->ImageWidth) ? (string)$this->xmlData->ImageWidth : false;
        $propertyType = 'int';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return int
     */
    public function GetImageHeight(){
        $value = isset($this->xmlData->ImageHeight) ? (string)$this->xmlData->ImageHeight : false;
        $propertyType = 'int';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
}