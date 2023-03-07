<?php

class OtapiChangePackageStatusResponse extends BaseOtapiType{
    /**
     * @return VoidOtapiAnswer
     */
    public function GetChangePackageStatusResult(){
        $value = isset($this->xmlData->ChangePackageStatusResult) ? $this->xmlData->ChangePackageStatusResult : false;
        return new VoidOtapiAnswer($value);
    }
}