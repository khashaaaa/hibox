<?php

class OtapiPackageAdminInfoListAnswer extends OtapiAnswer{
    /**
     * @return DataListOfOtapiPackageAdminInfo
     */
    public function GetResult(){
        $value = isset($this->xmlData->Result) ? $this->xmlData->Result : false;
        return new DataListOfOtapiPackageAdminInfo($value);
    }
}