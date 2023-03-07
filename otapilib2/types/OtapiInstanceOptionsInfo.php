<?php

class OtapiInstanceOptionsInfo extends BaseOtapiType{
    /**
     * @return boolean
     */
    public function IsEmailConfirmationUsed(){
        $value = isset($this->xmlData->IsEmailConfirmationUsed) ? (string)$this->xmlData->IsEmailConfirmationUsed : false;
        $propertyType = 'boolean';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetConfirmationCodeKind(){
        $value = isset($this->xmlData->ConfirmationCodeKind) ? (string)$this->xmlData->ConfirmationCodeKind : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return boolean
     */
    public function IsIPCheckUsed(){
        $value = isset($this->xmlData->IsIPCheckUsed) ? (string)$this->xmlData->IsIPCheckUsed : false;
        $propertyType = 'boolean';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return OtapiArrayOfString1
     */
    public function GetAllowedIPs(){
        $value = isset($this->xmlData->AllowedIPs) ? $this->xmlData->AllowedIPs : false;
        return new OtapiArrayOfString1($value);
    }
    /**
     * @return string
     */
    public function GetAdminPanelLanguage(){
        $value = isset($this->xmlData->AdminPanelLanguage) ? (string)$this->xmlData->AdminPanelLanguage : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return OtapiArrayOfNamedProperty
     */
    public function GetAvailableLanguages(){
        $value = isset($this->xmlData->AvailableLanguages) ? $this->xmlData->AvailableLanguages : false;
        return new OtapiArrayOfNamedProperty($value);
    }
    /**
     * @return OtapiArrayOfNamedProperty
     */
    public function GetFeatures(){
        $value = isset($this->xmlData->Features) ? $this->xmlData->Features : false;
        return new OtapiArrayOfNamedProperty($value);
    }
    /**
     * @return OtapiInstanceAccountInfo
     */
    public function GetAccount(){
        $value = isset($this->xmlData->Account) ? $this->xmlData->Account : false;
        return new OtapiInstanceAccountInfo($value);
    }
    /**
     * @return OtapiTariffInfo
     */
    public function GetTariff(){
        $value = isset($this->xmlData->Tariff) ? $this->xmlData->Tariff : false;
        return new OtapiTariffInfo($value);
    }
    /**
     * @return OtapiInstanceHostingInfo
     */
    public function GetHosting(){
        $value = isset($this->xmlData->Hosting) ? $this->xmlData->Hosting : false;
        return new OtapiInstanceHostingInfo($value);
    }
    /**
     * @return string
     */
    public function GetDefaultItemProvider(){
        $value = isset($this->xmlData->DefaultItemProvider) ? (string)$this->xmlData->DefaultItemProvider : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
}