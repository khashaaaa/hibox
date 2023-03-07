<?php

class OtapiRemoveBrandInfoResponse extends BaseOtapiType{
    /**
     * @return VoidOtapiAnswer
     */
    public function GetRemoveBrandInfoResult(){
        $value = isset($this->xmlData->RemoveBrandInfoResult) ? $this->xmlData->RemoveBrandInfoResult : false;
        return new VoidOtapiAnswer($value);
    }
}