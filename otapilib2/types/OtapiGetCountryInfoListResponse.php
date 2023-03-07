<?php

class OtapiGetCountryInfoListResponse extends BaseOtapiType{
    /**
     * @return OtapiCountryInfoListAnswer
     */
    public function GetGetCountryInfoListResult(){
        $value = isset($this->xmlData->GetCountryInfoListResult) ? $this->xmlData->GetCountryInfoListResult : false;
        return new OtapiCountryInfoListAnswer($value);
    }
}