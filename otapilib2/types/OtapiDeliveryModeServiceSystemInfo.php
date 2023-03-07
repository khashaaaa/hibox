<?php

class OtapiDeliveryModeServiceSystemInfo extends BaseOtapiType{
    /**
     * @return string
     */
    public function GetIntegrationDeliveryMode(){
        $value = isset($this->xmlData->IntegrationDeliveryMode) ? (string)$this->xmlData->IntegrationDeliveryMode : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetName(){
        $value = isset($this->xmlData->Name) ? (string)$this->xmlData->Name : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
}