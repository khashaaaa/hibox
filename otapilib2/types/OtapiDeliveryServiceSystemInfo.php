<?php

class OtapiDeliveryServiceSystemInfo extends BaseOtapiType{
    /**
     * @return string
     */
    public function GetIntegrationType(){
        $value = isset($this->xmlData->IntegrationType) ? (string)$this->xmlData->IntegrationType : false;
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
    public function IsPackageExportEnabled(){
        $value = isset($this->xmlData->IsPackageExportEnabled) ? (string)$this->xmlData->IsPackageExportEnabled : false;
        $propertyType = 'boolean';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return boolean
     */
    public function IsReceiptPrintEnabled(){
        $value = isset($this->xmlData->IsReceiptPrintEnabled) ? (string)$this->xmlData->IsReceiptPrintEnabled : false;
        $propertyType = 'boolean';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return boolean
     */
    public function NeedPackagesConfirming(){
        $value = isset($this->xmlData->NeedPackagesConfirming) ? (string)$this->xmlData->NeedPackagesConfirming : false;
        $propertyType = 'boolean';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return string
     */
    public function GetPackagesConfirmingActionName(){
        $value = isset($this->xmlData->PackagesConfirmingActionName) ? (string)$this->xmlData->PackagesConfirmingActionName : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return boolean
     */
    public function IsPickupPointSupported(){
        $value = isset($this->xmlData->IsPickupPointSupported) ? (string)$this->xmlData->IsPickupPointSupported : false;
        $propertyType = 'boolean';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return boolean
     */
    public function HasSettings(){
        $value = isset($this->xmlData->HasSettings) ? (string)$this->xmlData->HasSettings : false;
        $propertyType = 'boolean';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
}