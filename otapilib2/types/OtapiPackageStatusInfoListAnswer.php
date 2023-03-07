<?php

class OtapiPackageStatusInfoListAnswer extends OtapiAnswer{
    /**
     * @return OtapiDataListOfPackageStatusInfo
     */
    public function GetResult(){
        $value = isset($this->xmlData->Result) ? $this->xmlData->Result : false;
        return new OtapiDataListOfPackageStatusInfo($value);
    }
}