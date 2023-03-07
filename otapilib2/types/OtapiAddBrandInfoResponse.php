<?php

class OtapiAddBrandInfoResponse extends BaseOtapiType{
    /**
     * @return OtapiAddBrandInfoAnswer
     */
    public function GetAddBrandInfoResult(){
        $value = isset($this->xmlData->AddBrandInfoResult) ? $this->xmlData->AddBrandInfoResult : false;
        return new OtapiAddBrandInfoAnswer($value);
    }
}