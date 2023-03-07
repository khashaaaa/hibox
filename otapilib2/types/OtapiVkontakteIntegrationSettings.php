<?php

class OtapiVkontakteIntegrationSettings extends OtapiBaseExporterIntegrationSettings{
    /**
     * @return string
     */
    public function GetLogin(){
        $value = isset($this->xmlData->Login) ? (string)$this->xmlData->Login : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetPassword(){
        $value = isset($this->xmlData->Password) ? (string)$this->xmlData->Password : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return long
     */
    public function GetGroupId(){
        $value = isset($this->xmlData->GroupId) ? (string)$this->xmlData->GroupId : false;
        $propertyType = 'long';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetAlbumName(){
        $value = isset($this->xmlData->AlbumName) ? (string)$this->xmlData->AlbumName : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
}