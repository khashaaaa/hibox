<?php

class OtapiGetBrandInfoResponse extends BaseOtapiType{
    /**
     * @return OtapiBrandInfoAnswer
     */
    public function GetGetBrandInfoResult(){
        $value = isset($this->xmlData->GetBrandInfoResult) ? $this->xmlData->GetBrandInfoResult : false;
        return new OtapiBrandInfoAnswer($value);
    }
}