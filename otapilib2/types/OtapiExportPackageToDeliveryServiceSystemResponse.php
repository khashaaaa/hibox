<?php

class OtapiExportPackageToDeliveryServiceSystemResponse extends BaseOtapiType{
    /**
     * @return VoidOtapiAnswer
     */
    public function GetExportPackageToDeliveryServiceSystemResult(){
        $value = isset($this->xmlData->ExportPackageToDeliveryServiceSystemResult) ? $this->xmlData->ExportPackageToDeliveryServiceSystemResult : false;
        return new VoidOtapiAnswer($value);
    }
}