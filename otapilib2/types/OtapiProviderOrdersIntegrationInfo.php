<?php

class OtapiProviderOrdersIntegrationInfo extends BaseOtapiType{
    /**
     * @return boolean
     */
    public function IsAvailable(){
        $value = isset($this->xmlData->IsAvailable) ? (string)$this->xmlData->IsAvailable : false;
        $propertyType = 'boolean';
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
     * @return boolean
     */
    public function IsSessionRequired(){
        $value = isset($this->xmlData->IsSessionRequired) ? (string)$this->xmlData->IsSessionRequired : false;
        $propertyType = 'boolean';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return boolean
     */
    public function AllowCancelOrders(){
        $value = isset($this->xmlData->AllowCancelOrders) ? (string)$this->xmlData->AllowCancelOrders : false;
        $propertyType = 'boolean';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
}