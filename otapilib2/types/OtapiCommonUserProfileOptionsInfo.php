<?php

class OtapiCommonUserProfileOptionsInfo extends BaseOtapiType{
    /**
     * @return decimal
     */
    public function GetMaxProfilesCount(){
        $value = isset($this->xmlData->MaxProfilesCount) ? (string)$this->xmlData->MaxProfilesCount : false;
        $propertyType = 'decimal';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
}