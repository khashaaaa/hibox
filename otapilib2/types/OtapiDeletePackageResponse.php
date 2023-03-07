<?php

class OtapiDeletePackageResponse extends BaseOtapiType{
    /**
     * @return VoidOtapiAnswer
     */
    public function GetDeletePackageResult(){
        $value = isset($this->xmlData->DeletePackageResult) ? $this->xmlData->DeletePackageResult : false;
        return new VoidOtapiAnswer($value);
    }
}