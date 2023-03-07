<?php

class OtapiCreatePackageForOperatorResponse extends BaseOtapiType{
    /**
     * @return OtapiCreatePackageForOperatorAnswer
     */
    public function GetCreatePackageForOperatorResult(){
        $value = isset($this->xmlData->CreatePackageForOperatorResult) ? $this->xmlData->CreatePackageForOperatorResult : false;
        return new OtapiCreatePackageForOperatorAnswer($value);
    }
}