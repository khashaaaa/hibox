<?php

class OtapiCommonInstanceKeyOptionsInfo extends BaseOtapiType{
    /**
     * @return string
     */
    public function GetDefaultAdminPanelLanguage(){
        $value = isset($this->xmlData->DefaultAdminPanelLanguage) ? (string)$this->xmlData->DefaultAdminPanelLanguage : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return boolean
     */
    public function IsSellFree(){
        $value = isset($this->xmlData->IsSellFree) ? (string)$this->xmlData->IsSellFree : false;
        $propertyType = 'boolean';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
}