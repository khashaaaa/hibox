<?php

class OtapiPackageAdminInfoListFrameAnswer extends OtapiAnswer{
    /**
     * @return DataSubListOfOtapiPackageAdminInfo
     */
    public function GetResult(){
        $value = isset($this->xmlData->Result) ? $this->xmlData->Result : false;
        return new DataSubListOfOtapiPackageAdminInfo($value);
    }
}