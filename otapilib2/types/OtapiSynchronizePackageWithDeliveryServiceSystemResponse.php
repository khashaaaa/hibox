<?php

class OtapiSynchronizePackageWithDeliveryServiceSystemResponse extends BaseOtapiType{
    /**
     * @return VoidOtapiAnswer
     */
    public function GetSynchronizePackageWithDeliveryServiceSystemResult(){
        $value = isset($this->xmlData->SynchronizePackageWithDeliveryServiceSystemResult) ? $this->xmlData->SynchronizePackageWithDeliveryServiceSystemResult : false;
        return new VoidOtapiAnswer($value);
    }
}