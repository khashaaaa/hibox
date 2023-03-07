<?php

class OtapiEditBrandInfoResponse extends BaseOtapiType{
    /**
     * @return OtapiEditBrandInfoAnswer
     */
    public function GetEditBrandInfoResult(){
        $value = isset($this->xmlData->EditBrandInfoResult) ? $this->xmlData->EditBrandInfoResult : false;
        return new OtapiEditBrandInfoAnswer($value);
    }
}