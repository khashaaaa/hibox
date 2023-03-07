<?php

class DataListOfOtapiPackageAdminInfo extends BaseOtapiType{
    /**
     * @return ArrayOfOtapiPackageAdminInfo
     */
    public function GetContent(){
        $value = isset($this->xmlData->Content) ? $this->xmlData->Content : false;
        return new ArrayOfOtapiPackageAdminInfo($value);
    }
}