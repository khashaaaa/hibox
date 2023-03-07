<?php

class OtapiPackageTrackingInfo extends BaseOtapiType{
    /**
     * @return string
     */
    public function GetExternalTrackingUrl(){
        $value = isset($this->xmlData->ExternalTrackingUrl) ? (string)$this->xmlData->ExternalTrackingUrl : false;
        $propertyType = 'string';
        return $propertyType == 'boolean' ? $value == 'true' : $value;
    }
    /**
     * @return OtapiDataListOfPackageTrackingCheckpoint
     */
    public function GetPackageTrackingCheckpoints(){
        $value = isset($this->xmlData->PackageTrackingCheckpoints) ? $this->xmlData->PackageTrackingCheckpoints : false;
        return new OtapiDataListOfPackageTrackingCheckpoint($value);
    }
}